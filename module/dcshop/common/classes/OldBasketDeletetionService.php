<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 16.11.2015
 * Time: 09:43
 */
class OldBasketDeletetionService
{

    /**
     * @var PDOQueryWrapper
     */
    private $db;
    private static $queryFrame = <<<SQL
      call delete_old_baskets();
SQL;


    /**
     * OldBasketDeletetionService constructor.
     * @param PDOQueryWrapper $db
     */
    public function __construct(PDOQueryWrapper $db)
    {
        $this->db = $db;
    }

    public function __invoke()
    {
        $this->deleteOldBaskets();
    }

    public function deleteOldBaskets()
    {
        $this->db->setQuery(self::$queryFrame)->doQuery();
    }



}