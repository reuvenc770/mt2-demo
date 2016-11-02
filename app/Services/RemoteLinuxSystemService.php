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
    const FIND_RECENT_FILES_COMMAND = "find %s -mtime -1 -print";
    const LIST_DIRECTORIES_COMMAND = "find %s -type d -print ";
    const DIRECTORY_EXISTS_COMMAND = "[ -d %s ] && echo 1";

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

    public function createDirectoryCommand ( $directory ) {
        $command = sprintf( self::CREATE_DIR_COMMAND , $directory );
    
        ssh2_exec( $this->sshConnection , $command );
    }   
        
    public function createUserCommand ( $username , $directory ) {
        $command = sprintf( self::CREATE_USER_COMMAND , $directory , $username );
    
        ssh2_exec( $this->sshConnection , $command );
    }   
        
    public function setPasswordCommand ( $username , $password ) {
        $command = sprintf( self::SET_PASSWORD_COMMAND , $username , $password );
            
        ssh2_exec( $this->sshConnection , $command );
    }       
        
    public function setDirectoryOwnerCommand ( $username , $directory ) {
        $command = sprintf( self::CHANGE_DIR_OWNER_COMMAND , $username , $directory );
    
        ssh2_exec( $this->sshConnection , $command );
    }   
        
    public function setDirectoryPermissionsCommand ( $directory ) {
        $command = sprintf( self::CHANGE_DIR_PERMS_COMMAND , $directory );
    
        ssh2_exec( $this->sshConnection , $command );
    }   

    public function listDirectoriesCommand ( $directory ) {
        $command = sprintf( self::LIST_DIRECTORIES_COMMAND , $directory  );

        $stream = ssh2_exec( $this->sshConnection , $command );

        stream_set_blocking( $stream , true );
        $stream_out = ssh2_fetch_stream( $stream , SSH2_STREAM_STDIO );
        $directoryString = stream_get_contents($stream_out);

        $directoryList = explode( "\n" , $directoryString );

        return $directoryList;
    }

    public function directoryExists ( $directory ) {
        $command = sprintf( self::DIRECTORY_EXISTS_COMMAND , $directory );

        $stream = ssh2_exec( $this->sshConnection , $command );

        stream_set_blocking( $stream , true );
        $stream_out = ssh2_fetch_stream( $stream , SSH2_STREAM_STDIO );

        return ( stream_get_contents($stream_out) == 1 );
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
}
