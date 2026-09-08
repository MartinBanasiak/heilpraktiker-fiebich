<?php
namespace DynCom\dc\common\classes;
/**
 * Created by PhpStorm.
 * User: Micha
 * Date: 22.01.2015
 * Time: 05:54
 */

class SessionHandlerService {

    protected $site;
    protected $useSessionID;
    protected $sessionName;

    /**
     * @param Site $site
     */
    public function __construct( Site $site ) {
        $this->site = $site;
        if($this->site->use_session_id) {
            $this->useSessionID = TRUE;
            ini_set("session.use_cookies", 1);
            ini_set("session.use_only_cookies", 1);
            ini_set("session.use_trans_sid", 1);
        }
    }

    protected function _setSessionName() {
        
    }



}