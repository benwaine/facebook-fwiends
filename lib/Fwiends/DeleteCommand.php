<?php

namespace Fwiends;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Bwaine\FacebookTestUserClient\Client;
use Fwiends\FwiendsCommand;

class DeleteCommand extends FwiendsCommand {

    protected function configure() {
        $this->setName('fwiends:delete')
                ->setAliases(array('fwiends'))
                ->setDescription('Delete the group of fwiends in the supplied fwiends.lock file.')
                ->addArgument(
                        'appId', InputArgument::REQUIRED, 'The facebook ID of the app you wish to create fwiends for'
                )
                ->addArgument(
                        'appSecret', InputArgument::REQUIRED, 'The facebook APP SECRET of the app you wish to create fwiends for?'
                )
                ->addOption(
                        'fwiendsFile', null , InputOption::VALUE_OPTIONAL, 'Set the location of the lock file used ',  __DIR__ . '/fwiends.lock'
                )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $this->appId = $input->getArgument('appId');
        $this->appSecret = $input->getArgument('appSecret');
        
        $location = $input->getOption('fwiendsFile');
        $fwiends = $this->readCsv($location);
        
        $token = $this->getToken();
        
        foreach($fwiends as $fwiend) {
            $command = $this->client->getCommand('DeleteUser', array("access_token" => $token, "testUserId" => $fwiend[0]));
            $command->execute();
        }
        
        unlink($location);
        
        $output->write("FWIENDS GONE :(". PHP_EOL);
    }
    
    /**
     * Write each file to a csv file.
     * 
     * @param array $fwiends
     * @param string $location
     */
    protected function readCsv($location) {
        
        $handle = fopen($location, 'r');
        $fwiends = array();
        
        while (($line = fgetcsv($handle)) !== FALSE) {
            $fwiends[] = $line;
        }
        
        fclose($handle);
        
        return $fwiends;
    }

}