<?php
namespace DynCom\dc\common\interfaces;
/**
 * Created by PhpStorm.
 * User: Bauer
 * Date: 17.08.2015
 * Time: 09:09
 */
interface ServicePayload
{

    public function getRequestID();

    public function getServiceName();

    public function getStatusCode();

    public function getStatusMessage();

    public function getPayloadData();

    public function getDevErrors();

    public function getDevWarnings();

    public function getDevNotifications();

    public function getUserSuccessMsgs();

    public function getUserErrors();

    public function getUserWarnings();

    public function getUserNotifications();

}