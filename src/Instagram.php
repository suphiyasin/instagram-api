<?php
namespace SuphiGram\Instagram;
    class Instagram{

        public InstagramRequest     $request;
        public InstagramLogin       $login;
        public InstagramManuel      $manuel;
        public InstagramFunctions   $functions;
		public InstagramUser        $user;
		public InstagramWebApi      $webapi;

        public function __construct($username = null, $password = null){
            $this->request    = new InstagramRequest($username, $password, $this);
            $this->login      = new InstagramLogin($username, $password, $this);
            $this->manuel     = new InstagramManuel($username, $password, $this);
            $this->functions  = new InstagramFunctions($username, $password, $this);
            $this->user       =	new InstagramUser($username, $password, $this);
            $this->webapi     =	new InstagramWebApi($username, $password, $this);

        
        }
    }