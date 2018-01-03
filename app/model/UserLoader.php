<?php
namespace Acd\Model;

class UserLoader
{
    private function getManager()
    {
        switch (\Acd\conf::$DEFAULT_STORAGE) {
            case \Acd\conf::$STORAGE_TYPE_MONGODB:
                return new PersistentUserManagerMongoDB();
                break;
            case \Acd\conf::$STORAGE_TYPE_TEXTPLAIN:
                // TODO implement
                return new PersistentUserManagerTextPlain();
                break;
/*
            case \Acd\conf::$STORAGE_TYPE_MYSQL:
                // TODO implement
                return new PersistentEnumeratedManagerMySql();
                break;
*/
            default:
                throw new PersistentStorageUnknownInvalidException("Invalid type of persistent storage ".$this->getStorage().".");
                break;
        }
    }

    public function load($query)
    {
        $dataManager = $this->getManager();
        return $dataManager->load($query);
    }
    public function save($userDO)
    {
        $dataManager = $this->getManager();
        $NewUserDO = $dataManager->save($userDO);
        return $NewUserDO;
    }
    public function delete($id)
    {
        $dataManager = $this->getManager();
        return $dataManager->delete($id);
    }
   public function persist($userDO)
    {
        $dataManager = $this->getManager();
        return $dataManager->persist($userDO);
    }
}
