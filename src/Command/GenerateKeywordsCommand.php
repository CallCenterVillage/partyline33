<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-keywords',
    description: 'Generate keywords from knowledge base files'
)]
class GenerateKeywordsCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('Keywords Generator');
        
        $kbDir = 'kb';
        if (!is_dir($kbDir)) {
            $io->error("KB directory not found: {$kbDir}");
            return Command::FAILURE;
        }
        
        $keywords = [];
        
        // Read all markdown files from kb folder
        $kbFiles = glob($kbDir . '/*.md');
        foreach ($kbFiles as $file) {
            $io->text("Processing " . basename($file));
            
            $content = file_get_contents($file);
            if ($content === false) {
                $io->warning("Could not read {$file}");
                continue;
            }
            
            // Extract keywords from markdown content
            $fileKeywords = $this->extractKeywords($content);
            $keywords = array_merge($keywords, $fileKeywords);
        }
        
        // Remove duplicates and sort
        $keywords = array_unique($keywords);
        sort($keywords);
        
        // Write keywords to file
        $keywordsContent = implode(', ', $keywords);
        if (file_put_contents('dist/Keywords.txt', $keywordsContent) === false) {
            $io->error('Could not write dist/Keywords.txt');
            return Command::FAILURE;
        }
        
        $io->text("✓ Generated " . count($keywords) . " unique keywords");
        $io->success('Keywords.txt generated successfully!');
        
        return Command::SUCCESS;
    }
    
    private function extractKeywords(string $content): array
    {
        $keywords = [];
        
        // Extract words from headers (lines starting with #)
        preg_match_all('/^#{1,6}\s+(.+)$/m', $content, $matches);
        foreach ($matches[1] as $header) {
            $words = $this->extractWords($header);
            $keywords = array_merge($keywords, $words);
        }
        
        // Extract words from bold text (**word** or __word__)
        preg_match_all('/\*\*(.+?)\*\*/', $content, $matches);
        foreach ($matches[1] as $bold) {
            $words = $this->extractWords($bold);
            $keywords = array_merge($keywords, $words);
        }
        
        // Extract words from location patterns (W###, L#, etc.)
        preg_match_all('/\b(W\d{3}(?:-W\d{3})?)\b/', $content, $matches);
        $keywords = array_merge($keywords, $matches[1]);
        
        preg_match_all('/\b(L\d)\b/', $content, $matches);
        $keywords = array_merge($keywords, $matches[1]);
        
        // Extract event names and special terms
        preg_match_all('/\b([A-Z]{2,}(?:\s+[A-Z0-9]+)*)\b/', $content, $matches);
        foreach ($matches[1] as $term) {
            if (strlen($term) > 2 && !in_array($term, ['THE', 'AND', 'FOR', 'ARE', 'YOU', 'ALL', 'CAN', 'GET', 'SEE', 'NOW', 'DAY', 'NEW', 'ONE', 'TWO', 'FRI', 'SAT', 'SUN', 'MON', 'TUE', 'WED', 'THU'])) {
                $keywords[] = $term;
            }
        }
        
        return $keywords;
    }
    
    private function extractWords(string $text): array
    {
        // Split text into words and filter
        $words = preg_split('/[\s,\-–—]+/', $text);
        $filtered = [];
        
        foreach ($words as $word) {
            $word = trim($word, '.,!?;:()[]{}"\'-');
            if (strlen($word) > 2 && !is_numeric($word)) {
                $filtered[] = $word;
            }
        }
        
        return $filtered;
    }
} 