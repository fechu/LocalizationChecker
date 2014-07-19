<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use LocalizationChecker\Command\StringsCheckerCommand;

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

    public function testThrowsExceptionWhenSyntaxInCommentIsWrong()
    {
        $commandTester = $this->executeCommand("tests/resources/InvalidCommentSyntax.strings");

        $this->assertEquals(
            1, 
            $commandTester->getStatusCode(),
            "Should return 1 if a Syntax error occured in one file."
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
        $application->add(new StringsCheckerCommand());

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
