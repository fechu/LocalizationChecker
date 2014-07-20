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

    public function testStatusCodeIsCorrectIfOnePathIsNotAFolder()
    {
        $commandTester = $this->executeCommand(array(
            "tests/resources/CorrectSyntax.strings" // Not a folder
        ));

        $this->assertEquals(
            -1, 
            $commandTester->getStatusCode(),
            "Should return -1 if one of the given paths is not a folder"
        );
    }

    public function testStatusCodeIfFileMissingInAFolder()
    {
        $commandTester = $this->executeCommand(array(
            "tests/resources/en-MissingFile.lproj",
            "tests/resources/de-MissingFile.lproj"
        ));

        $this->assertEquals(
            1,
            $commandTester->getStatusCode(),
            "Should return 1 as there's 1 file missing"
        );
        
    }
    
    public function testStatusCodeIfMultipleFilesMissingInAFolder()
    {
        $commandTester = $this->executeCommand(array(
            "tests/resources/en-TwoMissingFiles.lproj",
            "tests/resources/de-TwoMissingFiles.lproj"
        ));

        $this->assertEquals(
            2,
            $commandTester->getStatusCode(),
            "Should return 2 as there are 2 files missing"
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
