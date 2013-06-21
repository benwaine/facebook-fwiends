<?php
namespace Fwiends;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Bwaine\FacebookTestUserClient\Client;

abstract class FwiendsCommand extends SymfonyCommand {

    protected $client;
    
    protected $appId;
    
    protected $appSecret;
    
    public function __construct(Client $client) {
        parent::__construct();
        $this->client = $client;
        
    }
    
    protected function getToken() {
        
        $authCommand = $this->client->getCommand('ObtainAppAccessToken', array(
            "client_id" => $this->appId,
            "client_secret" => $this->appSecret));

        try {
            $response = $authCommand->execute();
        } catch (\Exception $e) {
            throw new \RuntimeException('Command Failed!', null, $e);
        }

        $token = $response['access_token'];
        
        return $token;
        
    }
}


