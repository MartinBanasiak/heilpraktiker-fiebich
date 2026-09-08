<?php
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 6/19/2015
 * Time: 1:50 PM
 */
include('C:\PHPStorm Projects\DcShop-NAV2015\dc\common\interfaces\GenericDBQueryWrapperInterface.php');
include('C:\PHPStorm Projects\DcShop-NAV2015\dc\common\classes\PDOQueryWrapper.php');
$qw = new PDOQueryWrapper('icg-shop',3506,'dcshop','ShopConnect','1q2w3e4r5t');
$query =
    $qw->select('svai.id,svai.item_no,sicr.item_reference_no')
        ->from('shop_view_active_item','svai')
        ->join('left','shop_item_cross_reference','sicr')
            ->enterParentheses()
                ->on('sicr.company','=','svai.company')
                ->andOn('sicr.item_no','=','svai.item_no')
            ->leaveParentheses()
            ->enterParentheses('OR')
                ->on('sicr.company','=','svai.company')
                ->andOn('sicr.item_no','=','svai.parent_item_no')
            ->leaveParentheses()
        ->where('svai.language_code','=','DEU')
        ->andWhere('svai.id','>',10)
        ->orderBy('svai.item_no','asc')
        ->getConstructedQuery();
echo $query;