<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use LocalizationChecker\Command\StringsCommand;

class StringsCheckerCommandTest extends \PHPUnit_Framework_TestCase
{

    public function testValidatesSyntaxOfSingleCorrectFile()
    {
        $commandTester = $this->executeCommand("tests/resources/CorrectSyntax.strings");

        $this->assertEquals(
            0, 
            $commandTester->getStatusCode(), 
            "Should return a 0 code when the given file has a correct syntax."
        );
    }


    public function testStatusCodeContainsNumberOfErrors1()
    {
        $commandTester = $this->executeCommand("tests/resources/InvalidCommentSyntax.strings");

        $this->assertEquals(
            1, 
            $commandTester->getStatusCode(),
            "Should return 1 if a Syntax error occured in one file."
        );
    }

    
    public function testStatusCodeContainsNumberOfErrors2()
    {
        $commandTester = $this->executeCommand(array(
            "tests/resources/InvalidCommentSyntax.strings",
            "tests/resources/InvalidTranslationEntrySyntax.strings"
        ));

        $this->assertEquals(
            2, 
            $commandTester->getStatusCode(),
            "Should return 2 if a Syntax error occured in one file."
        );
    }

    public function testReturnsNonzeroStatuscodeWhenKeysDontExist()
    {
        $commandTester = $this->executeCommand(array(
            "tests/resources/en.lproj/MissingKey.strings",
            "tests/resources/de.lproj/MissingKey.strings"
        ));

        $this->assertEquals(
            1, 
            $commandTester->getStatusCode(),
            "Should return 1 as status code if a key is missing."
        );
    }
    
    

    ////////////////////////////////////////////////////////////////////////
    // Helper
    ////////////////////////////////////////////////////////////////////////

    /**
     * Executes the StringsCheckerCommand with the given files. 
     *
     * @param array|string  $files  A single path to a file or an array of files. These files
     *                              get added as the arguments to the command before executing.
     *
     * @return CommandTester    The command tester that executed the command with the given
     *                          arguments. 
     */
    protected function executeCommand($files)
    {
        // Make sure argument is wrapped in an array.
        if (!is_array($files)) {
            $files = array($files);
        }

        $application = new Application();
        $application->add(new StringsCommand());

        $command = $application->find('check:strings');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                'command'   => $command->getName(),
                'files'     => $files
            )
        );

        return $commandTester;
    }
}
