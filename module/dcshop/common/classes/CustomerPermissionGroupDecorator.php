<?php
namespace DynCom\dc\dcShop\classes;
use DynCom\dc\common\classes\PDOQueryWrapper;
use DynCom\dc\dcShop\abstracts\CustomerDecorator;
use DynCom\dc\dcShop\interfaces\CustomerInterface;
use DynCom\dc\dcShop\interfaces\CustomerWithPermissionGroupsInterface;

/**
 * Created by PhpStorm.
 * User: bauer
 * Date: 12.10.2015
 * Time: 12:11
 */
class CustomerPermissionGroupDecorator extends CustomerDecorator implements CustomerWithPermissionGroupsInterface
{

    /**
     * @var PDOQueryWrapper
     */
    private $db;
    private $permissionGroupCodes = [];

    /**
     * CustomerPermissionGroupDecorator constructor.
     * @param CustomerInterface $customer
     * @param PDOQueryWrapper $db
     */
    public function __construct(CustomerInterface $customer, PDOQueryWrapper $db)
    {
        if($customer instanceof CustomerWithPermissionGroupsInterface) {
            throw new \InvalidArgumentException('Customer is already decorated with PermissionsGroups.');
        }
        parent::__construct($customer);
        $this->db = $db;
        $this->fetchPermissionGroupCodes();
    }

    private function fetchPermissionGroupCodes()
    {
        if($this->decoratedEntity->getID() > 0) {
            $this->permissionGroupCodes = [];
            $this->db
                ->select('permission_group_code')
                ->from('shop_permissions_group_link')
                ->where('type', '=', 0)
                ->andWhere('customer_no', '=', $this->decoratedEntity->getCustomerNo())
                ->setConstructedQuery()->doQuery();
            $resArr = $this->db->getResultArray();
            foreach ($resArr as $row) {
                $this->permissionGroupCodes[] = $row['permission_group_code'];
            }
        }
    }

    /**
     * @return array
     */
    public function getPermissionGroupCodes()
    {
        return $this->permissionGroupCodes;
    }

}