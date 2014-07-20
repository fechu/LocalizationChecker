<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use LocalizationChecker\Command\StringsFolderCommand;

class StringsFolderCommandTest extends \PHPUnit_Framework_TestCase
{

    public function testStatusCodeIsCorrectIfNoFoldersGiven()
    {
        $commandTester = $this->executeCommand(array());

        $this->assertEquals(
            -1, 
            $commandTester->getStatusCode(),
            "Should return -1 if no folders given to check."
        );
    }
    


    ////////////////////////////////////////////////////////////////////////
    // Helper
    ////////////////////////////////////////////////////////////////////////

    /**
     * Executes the StringsFolderCheckerCommand with the given folders. 
     *
     * @param array|string  $files  A single path to a folder or an array of folders. These folders
     *                              get added as the arguments to the command before executing.
     *
     * @return CommandTester    The command tester that executed the command with the given
     *                          arguments. 
     */
    protected function executeCommand($folders)
    {
        // Make sure argument is wrapped in an array.
        if (!is_array($folders)) {
            $folders = array($folders);
        }

        $application = new Application();
        $application->add(new StringsFolderCommand());

        $command = $application->find('check:strings');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                'command'   => $command->getName(),
                'folders'   => $folders
            )
        );

        return $commandTester;
    }
}
