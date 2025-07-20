<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:build-agent',
    description: 'Build complete agent package: generate keywords, pronunciation dictionary, and compile system prompt'
)]
class BuildAgentCommand extends Command
{


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('DEF CON 33 Agent Builder');
        $io->text('Building complete agent package for ElevenLabs...');
        
        $application = $this->getApplication();
        if ($application === null) {
            $io->error('Application not available');
            return Command::FAILURE;
        }
        
        // Step 1: Generate Keywords
        $io->section('Step 1: Generating Keywords');
        $command = $application->find('app:generate-keywords');
        $result = $command->run($input, $output);
        if ($result !== Command::SUCCESS) {
            $io->error('Failed to generate keywords');
            return $result;
        }
        
        // Step 2: Generate Pronunciation Dictionary
        $io->section('Step 2: Generating Pronunciation Dictionary');
        $command = $application->find('app:generate-pronunciation-dictionary');
        $result = $command->run($input, $output);
        if ($result !== Command::SUCCESS) {
            $io->error('Failed to generate pronunciation dictionary');
            return $result;
        }
        
        // Step 3: Compile System Prompt
        $io->section('Step 3: Compiling System Prompt');
        $command = $application->find('app:compile-agent');
        $result = $command->run($input, $output);
        if ($result !== Command::SUCCESS) {
            $io->error('Failed to compile system prompt');
            return $result;
        }
        
        $io->success('Agent build complete!');
        $io->text('All files are ready in the dist/ folder:');
        $io->listing([
            'SystemPrompt.txt - Complete system prompt',
            'Keywords.txt - Extracted keywords',
            'Opener.txt - Agent opener message',
            'pronunciation-dictionary.xml - Pronunciation guide'
        ]);
        
        return Command::SUCCESS;
    }
} 