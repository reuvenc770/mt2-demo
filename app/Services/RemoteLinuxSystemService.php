<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services;

class RemoteLinuxSystemService {
    const CREATE_USER_COMMAND = "useradd -g sftp -d %s %s";
    const SET_PASSWORD_COMMAND = "echo %s:%s | chpasswd";
    const CREATE_DIR_COMMAND = "mkdir %s";
    const CHANGE_DIR_OWNER_COMMAND = "chown -R %s:sftp %s";
    const CHANGE_DIR_PERMS_COMMAND = "chmod 755 %s";
    const FIND_RECENT_FILES_COMMAND = "find %s -type f -mtime -1 -print";
    const LIST_DIRECTORIES_COMMAND = "find %s -type d -print ";
    const DIRECTORY_EXISTS_COMMAND = "[ -d %s ] && echo 1";
    const GET_CONTENT_SLICE_COMMAND = "sed -n %d,%dp %s";
    const GET_FILE_LINE_COUNT_COMMAND = "wc -l < %s";
    const APPEND_EOF_COMMAND = "sed -i -e '\$a\' %s";

    protected $sshConnection = null;
    protected $host = null;
    protected $port = null;
    protected $sshUser = null;
    protected $sshPublicKey = null;
    protected $sshPrivateKey = null;

    public function __construct () {}

    public function init ( $host , $port , $sshUser , $sshPublicKey , $sshPrivateKey ) {
        $this->host = $host;
        $this->port = $port;
        $this->sshUser = $sshUser;
        $this->sshPublicKey = $sshPublicKey;
        $this->sshPrivateKey = $sshPrivateKey;

        $this->initSshConnection();

        return $this->sshConnection;
    }

    public function createDirectory ( $directory ) {
        $command = sprintf( self::CREATE_DIR_COMMAND , $directory );
    
        ssh2_exec( $this->sshConnection , $command );
    }   
        
    public function createUser ( $username , $directory ) {
        $command = sprintf( self::CREATE_USER_COMMAND , $directory , $username );
    
        ssh2_exec( $this->sshConnection , $command );
    }   
        
    public function setPassword ( $username , $password ) {
        $command = sprintf( self::SET_PASSWORD_COMMAND , $username , $password );
            
        ssh2_exec( $this->sshConnection , $command );
    }       
        
    public function setDirectoryOwner ( $username , $directory ) {
        $command = sprintf( self::CHANGE_DIR_OWNER_COMMAND , $username , $directory );
    
        ssh2_exec( $this->sshConnection , $command );
    }   
        
    public function setDirectoryPermissions ( $directory ) {
        $command = sprintf( self::CHANGE_DIR_PERMS_COMMAND , $directory );
    
        ssh2_exec( $this->sshConnection , $command );
    }   

    public function listDirectories ( $directory ) {
        $command = sprintf( self::LIST_DIRECTORIES_COMMAND , $directory  );

        $stream = ssh2_exec( $this->sshConnection , $command );

        $directoryString = $this->getOutput( $stream );

        $directoryList = explode( "\n" , $directoryString );

        return $directoryList;
    }

    public function appendEofToFile ( $filePath ) {
        $command = sprintf( self::APPEND_EOF_COMMAND , $filePath );
    
        ssh2_exec( $this->sshConnection , $command );
    }

    public function getFileContentSlice ( $filePath , $firstLine , $lastLine ) {
        $command = sprintf( self::GET_CONTENT_SLICE_COMMAND , $firstLine , $lastLine , $filePath );

        $stream = ssh2_exec( $this->sshConnection , $command );

        $contentString = $this->getOutput( $stream );

        $lineArray = explode( "\n" , $contentString );

        $lastIndex = count( $lineArray ) - 1;
        if ( isset( $lineArray[ $lastIndex ] ) && empty( $lineArray[ $lastIndex ] ) ) {
            array_pop( $lineArray );
        }

        return $lineArray;
    }

    public function getFileLineCount ( $filePath ) {
        $command = sprintf( self::GET_FILE_LINE_COUNT_COMMAND , $filePath );

        $stream = ssh2_exec( $this->sshConnection , $command );

        $contentString = $this->getOutput( $stream );

        return (int) trim( $contentString );
    }

    public function directoryExists ( $directory ) {
        $command = sprintf( self::DIRECTORY_EXISTS_COMMAND , $directory );

        $stream = ssh2_exec( $this->sshConnection , $command );

        return ( $this->getOutput( $stream ) == 1 );
    }

    public function getRecentFiles ( $directory ) {
        $command = sprintf( self::FIND_RECENT_FILES_COMMAND , $directory );

        $stream = ssh2_exec( $this->sshConnection , $command );

        return $this->getOutput( $stream );
    }

    protected function initSshConnection () {
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

    protected function getOutput ( $stream ) {
        stream_set_blocking( $stream , true );
        $stream_out = ssh2_fetch_stream( $stream , SSH2_STREAM_STDIO );
        return stream_get_contents( $stream_out );
    }
}
