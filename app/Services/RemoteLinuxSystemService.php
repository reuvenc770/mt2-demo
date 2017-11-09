<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services;

class RemoteLinuxSystemService {
    const CREATE_USER_COMMAND = "sudo useradd -g sftponly -d %s %s";
    const SET_PASSWORD_COMMAND = "echo %s:%s | sudo chpasswd";
    const CREATE_DIR_COMMAND = "sudo mkdir %s";
    const CHANGE_DIR_OWNER_COMMAND = "sudo chown -R %s:sftponly %s";
    const CHANGE_DIR_PERMS_COMMAND = "sudo chmod 755 %s";
    const FIND_RECENT_FILES_COMMAND = "sudo find %s %s"; 
    const LIST_DIRECTORIES_COMMAND = "sudo find %s -type d -print ";
    const DIRECTORY_EXISTS_COMMAND = "[ -d %s ] && echo 1";
    const FILE_EXISTS_COMMAND = "[ -f %s ] && echo 1";
    const GET_CONTENT_SLICE_COMMAND = "sudo sed -n %d,%dp %s";
    const GET_FILE_LINE_COUNT_COMMAND = "sudo cat %s | wc -l";
    const APPEND_EOF_COMMAND = "sudo sed -i -e '\$a\' %s";
    const USER_EXISTS_COMMAND = 'getent passwd %s > /dev/null 2&>1; [[ $? -eq 0 ]] && echo "{\"status\":1}" || echo "{\"status\":0}"';
    const MOVE_FILE_COMMAND = 'sudo mv %s %s';
    const DELETE_FILE_COMMAND = 'sudo rm %s';

    const PSEUDO_TTY_FLAG = true; #for nologin users w/ sudo access

    protected $sshConnection = null;
    protected $host = null;
    protected $port = null;
    protected $sshUser = null;
    protected $sshPublicKey = null;
    protected $sshPrivateKey = null;

    public function __construct () {}

    public function initSshConnection ( $host , $port , $sshUser , $sshPublicKey , $sshPrivateKey ) {
        $this->host = $host;
        $this->port = $port;
        $this->sshUser = $sshUser;
        $this->sshPublicKey = $sshPublicKey;
        $this->sshPrivateKey = $sshPrivateKey;

        if ( is_null( $this->host ) ) { throw new \Exception( "Server Host is required." ); }
        if ( is_null( $this->port ) ) { throw new \Exception( "Server Port is required." ); }
        if ( is_null( $this->sshUser ) ) { throw new \Exception( "SSH user is required." ); }
        if ( is_null( $this->sshPublicKey ) ) { throw new \Exception( "SSH public key is required." ); }
        if ( is_null( $this->sshPrivateKey ) ) { throw new \Exception( "SSH private key is required." ); }
        
        $this->sshConnection = ssh2_connect( $this->host , $this->port , [ 'hostkey' => 'ssh-rsa' ] );

        if ( $this->sshConnection === false ) { throw new \Exception( "Failed to connect to server: {$this->sshUser}@{$this->host}:{$this->port}" ); }

        $authSuccess = ssh2_auth_pubkey_file(
            $this->sshConnection ,
            $this->sshUser ,
            $this->sshPublicKey ,
            $this->sshPrivateKey
        );

        if ( $authSuccess === false ) { throw new \Exception( "Failed to authenticate with the server." ); }
    }

    public function connectionExists () {
        return isset( $this->sshConnection );
    }

    public function createDirectory ( $directory ) {
        $command = sprintf( self::CREATE_DIR_COMMAND , $this->cleanPath( $directory ) );
    
        ssh2_exec( $this->sshConnection , $command , self::PSEUDO_TTY_FLAG );
    }   
        
    public function createUser ( $username , $directory ) {
        $command = sprintf( self::CREATE_USER_COMMAND , $this->cleanPath( $directory ) , trim( $username ) );
    
        ssh2_exec( $this->sshConnection , $command );
    }   
        
    public function setPassword ( $username , $password ) {
        $command = sprintf( self::SET_PASSWORD_COMMAND , trim( $username ) , trim( $password ) );
            
        ssh2_exec( $this->sshConnection , $command );
    }       
        
    public function setDirectoryOwner ( $username , $directory ) {
        $command = sprintf( self::CHANGE_DIR_OWNER_COMMAND , trim( $username ) , $this->cleanPath( $directory ) );
    
        ssh2_exec( $this->sshConnection , $command );
    }   
        
    public function setDirectoryPermissions ( $directory ) {
        $command = sprintf( self::CHANGE_DIR_PERMS_COMMAND , $this->cleanPath( $directory ) );
    
        ssh2_exec( $this->sshConnection , $command );
    }   

    public function listDirectories ( $directory ) {
        $command = sprintf( self::LIST_DIRECTORIES_COMMAND , $this->cleanPath( $directory ) );

        $stream = ssh2_exec( $this->sshConnection , $command , self::PSEUDO_TTY_FLAG );

        $directoryString = $this->getOutput( $stream );

        $directoryList = explode( PHP_EOL , $directoryString );

        return $directoryList;
    }

    public function appendEofToFile ( $filePath ) {
        $command = sprintf( self::APPEND_EOF_COMMAND , $this->cleanPath( $filePath ) );
    
        ssh2_exec( $this->sshConnection , $command , self::PSEUDO_TTY_FLAG );
    }

    public function getFileContentSlice ( $filePath , $firstLine , $lastLine ) {
        $command = sprintf( self::GET_CONTENT_SLICE_COMMAND , $firstLine , $lastLine , $this->cleanPath( $filePath ) );

        $stream = ssh2_exec( $this->sshConnection , $command , self::PSEUDO_TTY_FLAG );

        $contentString = $this->getOutput( $stream );

        $lineArray = explode( PHP_EOL , $contentString );

        $lastIndex = count( $lineArray ) - 1;
        if ( isset( $lineArray[ $lastIndex ] ) && empty( $lineArray[ $lastIndex ] ) ) {
            array_pop( $lineArray );
        }

        return $lineArray;
    }

    public function getFileLineCount ( $filePath ) {
        $command = sprintf( self::GET_FILE_LINE_COUNT_COMMAND , $this->cleanPath( $filePath ) );

        $stream = ssh2_exec( $this->sshConnection , $command , self::PSEUDO_TTY_FLAG );

        $contentString = $this->getOutput( $stream );

        return (int) trim( $contentString );
    }

    public function directoryExists ( $directory ) {
        $command = sprintf( self::DIRECTORY_EXISTS_COMMAND , $this->cleanPath( $directory ) );

        $stream = ssh2_exec( $this->sshConnection , $command );

        return ( $this->getOutput( $stream ) == 1 );
    }

    public function fileExists ( $filepath ) {
        $command = sprintf( self::FILE_EXISTS_COMMAND , $this->cleanPath( $filepath ) );

        $stream = ssh2_exec( $this->sshConnection , $command );

        return ( $this->getOutput( $stream ) == 1 );
    }

    public function getRecentFiles ( $directory , $options = null ) {
        $defaultOptions = [
            '-type f' ,
            '-mtime -1' ,
            '-print'
        ];

        if ( !is_null( $options ) ) {
            $defaultOptions = $options;
        }

        $optionString = implode( ' ' , $defaultOptions );

        $command = sprintf( self::FIND_RECENT_FILES_COMMAND , $this->cleanPath( $directory ) , $optionString );

        $stream = ssh2_exec( $this->sshConnection , $command ,  self::PSEUDO_TTY_FLAG );

        return $this->getOutput( $stream );
    }

    public function userExists ( $username ) {
        $command = sprintf( self::USER_EXISTS_COMMAND , trim( $username ) );

        $stream = ssh2_exec( $this->sshConnection , $command );

        return $this->getOutput( $stream );
    }

    public function moveFile ( $source , $destination ) {
        $command = sprintf( self::MOVE_FILE_COMMAND , $this->cleanPath( $source ) , $this->cleanPath( $destination ) );

        $stream = ssh2_exec( $this->sshConnection , $command , self::PSEUDO_TTY_FLAG );

        return $this->getOutput( $stream , SSH2_STREAM_STDERR );
    }

    public function deleteFile ( $file ) {
        $command = sprintf( self::DELETE_FILE_COMMAND , $this->cleanPath( $file ) );

        $stream = ssh2_exec( $this->sshConnection , $command , self::PSEUDO_TTY_FLAG );

        return $this->getOutput( $stream , SSH2_STREAM_STDERR );
    }

    protected function getOutput ( $stream , $streamId = SSH2_STREAM_STDIO ) {
        stream_set_blocking( $stream , true );
        $stream_out = ssh2_fetch_stream( $stream , $streamId );
        return stream_get_contents( $stream_out );
    }
    
    protected function cleanPath ( $path ) {
        $escapedPath = escapeshellcmd( trim( $path ) );
        return str_replace( ' ' , '\ ' , $escapedPath );
    }
}
