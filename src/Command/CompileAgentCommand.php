<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:compile-agent',
    description: 'Compile agent system prompt from configuration and knowledge base files'
)]
class CompileAgentCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('Agent Compiler');
        
        // Read the base system prompt
        $systemPrompt = file_get_contents('agent/System.md');
        if ($systemPrompt === false) {
            $io->error('Could not read agent/System.md');
            return Command::FAILURE;
        }
        
        // Read all agent configuration files
        $agentFiles = [
            'agent/Personality.md',
            'agent/Environment.md', 
            'agent/Tone.md',
            'agent/Goal.md',
            'agent/Guardrails.md',
            'agent/Tools.md'
        ];
        
        $agentContent = '';
        foreach ($agentFiles as $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                if ($content !== false) {
                    $agentContent .= "\n\n" . $content;
                    $io->text("✓ Added " . basename($file));
                }
            } else {
                $io->warning("File not found: {$file}");
            }
        }
        
        // Read all files from the kb folder
        $kbDir = 'kb';
        $kbContent = '';
        
        if (is_dir($kbDir)) {
            $kbFiles = glob($kbDir . '/*.md');
            foreach ($kbFiles as $file) {
                $content = file_get_contents($file);
                if ($content !== false) {
                    $kbContent .= "\n\n" . $content;
                    $io->text("✓ Added " . basename($file));
                }
            }
        } else {
            $io->warning("KB directory not found: {$kbDir}");
        }
        
        // Compile the full system prompt
        $compiledSystemPrompt = $systemPrompt . $agentContent . $kbContent;
        
        // Write the compiled system prompt
        if (file_put_contents('dist/SystemPrompt.txt', $compiledSystemPrompt) === false) {
            $io->error('Could not write dist/SystemPrompt.txt');
            return Command::FAILURE;
        }
        
        $io->text('✓ Generated dist/SystemPrompt.txt');
        
        // Copy other agent files
        $filesToCopy = [
            'agent/Opener.md' => 'dist/Opener.txt'
        ];
        
        foreach ($filesToCopy as $source => $destination) {
            if (!file_exists($source)) {
                $io->warning("Source file not found: {$source}");
                continue;
            }
            
            if (copy($source, $destination) === false) {
                $io->error("Could not copy {$source} to {$destination}");
                continue;
            }
            
            $io->text("✓ Copied " . basename($source) . " to dist/");
        }
        
        $io->success('Agent compilation complete!');
        $io->text('All files are ready in the dist/ folder for deployment');
        
        return Command::SUCCESS;
    }
} 