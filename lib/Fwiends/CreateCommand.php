<?php

namespace Fwiends;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Bwaine\FacebookTestUserClient\Client;
use Fwiends\FwiendsCommand;

class CreateCommand extends FwiendsCommand {

    protected function configure() {
        $this->setName('fwiends:create')
                ->setAliases(array('fwiends'))
                ->setDescription('Create a group of fwiends to test your facebook application')
                ->addArgument(
                        'appId', InputArgument::REQUIRED, 'The facebook ID of the app you wish to create fwiends for'
                )
                ->addArgument(
                        'appSecret', InputArgument::REQUIRED, 'The facebook APP SECRET of the app you wish to create fwiends for?'
                )
                ->addOption(
                        'fwiendsFile', null, InputOption::VALUE_OPTIONAL, 'Set the location of the lock file created', __DIR__ . '/fwiends.lock'
                )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $this->appId = $input->getArgument('appId');
        $this->appSecret = $input->getArgument('appSecret');

        $token = $this->getToken();

        // Create Fwiends for app
        $names = array('Billie', 'Nay', "Will", "Raj", "Robyn");

        $fwiends = array();

        for ($i = 0; $i <= 4; $i++) {

            $command = $this->client->getCommand('CreateUser', array(
                "appId" => $this->appId,
                "access_token" => $token,
                "name" => $names[$i],
                "installed" => true
                    ));

            try {
                $response = $command->execute();
                $fwiends[$names[$i]] = array(
                    $response['id'],
                    $response['access_token'],
                    $response['email'],
                    $response['password']
                );
            } catch (\Exception $e) {
                // Handle failure here
                $output->write("Error: Fwiends not created:" . $e->getMessage(). PHP_EOL);
                return;
            }
        }

        $this->writeCsv($fwiends, $input->getOption('fwiendsFile'));
        $this->createRelationships($fwiends);
        
        $output->write("FWIENDS!" . PHP_EOL);
    }

    protected function createRelationships($fwiends) {


        foreach ($fwiends as $name => $fwiend) {
            switch ($name):

                case "Billie":
                    $this->createReleationship($fwiends['Billie'], $fwiends['Nay']);
                    $this->createReleationship($fwiends['Billie'], $fwiends['Will']);
                    $this->createReleationship($fwiends['Billie'], $fwiends['Raj']);
                    $this->createReleationship($fwiends['Billie'], $fwiends['Robyn']);
                    break;
                case "Nay":
                    $this->createReleationship($fwiends['Nay'], $fwiends['Will']);
                    break;
                case "Will":
                    break;
                case "Raj":
                    $this->createReleationship($fwiends['Raj'], $fwiends['Will']);
                    break;
                case "Robyn":
                    break;

            endswitch;
        }
    }

    protected function createReleationship($fwiendA, $fwiendB) {
        $connectCommand1 = $this->client->getCommand('ConnectFriend', array(
            "user1" => $fwiendA[0],
            "user2" => $fwiendB[0],
            "access_token" => $fwiendA[1]
                ));

        $connectCommand2 = $this->client->getCommand('ConnectFriend', array(
            "user1" => $fwiendB[0],
            "user2" => $fwiendA[0],
            "access_token" => $fwiendB[1]
                ));

        try {
            $resp1 = $connectCommand1->execute();
            $resp2 = $connectCommand2->execute();
        } catch (\Exception $e) {
            // Handle failure here.
            throw new \Exception("Failed creating friends relationship!", null, $e);
        }

        $success = ($resp1['result'] && $resp2['result']);
        
        return $success;
    }

    /**
     * Write each file to a csv file.
     * 
     * @param array $fwiends
     * @param string $location
     */
    protected function writeCsv($fwiends, $location) {

        $handle = fopen($location, 'w');


        foreach ($fwiends as $fwiend) {
            fputcsv($handle, $fwiend);
        }

        fclose($handle);
    }

}