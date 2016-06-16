<?php
namespace App\Library\Bronto;
class updateMessageFolders
{

    /**
     * @var messageFolderObject[] $messageFolders
     */
    protected $messageFolders = null;

    /**
     * @param messageFolderObject[] $messageFolders
     */
    public function __construct(array $messageFolders)
    {
      $this->messageFolders = $messageFolders;
    }

    /**
     * @return messageFolderObject[]
     */
    public function getMessageFolders()
    {
      return $this->messageFolders;
    }

    /**
     * @param messageFolderObject[] $messageFolders
     * @return updateMessageFolders
     */
    public function setMessageFolders(array $messageFolders)
    {
      $this->messageFolders = $messageFolders;
      return $this;
    }

}
