<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-pronunciation-dictionary',
    description: 'Generate pronunciation-dictionary.xml from all markdown files in the kb folder'
)]
class GeneratePronunciationDictionaryCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('DEF CON 33 Pronunciation Dictionary Generator');
        
        $kbDir = 'kb';
        if (!is_dir($kbDir)) {
            $io->error("KB directory not found: {$kbDir}");
            return Command::FAILURE;
        }
        
        $entries = [];
        
        // Read all markdown files from kb folder
        $kbFiles = glob($kbDir . '/*.md');
        foreach ($kbFiles as $file) {
            $io->text("Processing " . basename($file));
            
            $content = file_get_contents($file);
            if ($content === false) {
                $io->warning("Could not read {$file}");
                continue;
            }
            
            // Extract pronunciation entries from markdown content
            $fileEntries = $this->extractPronunciationEntries($content);
            $entries = array_merge($entries, $fileEntries);
        }
        
        // Remove duplicates based on grapheme
        $uniqueEntries = [];
        foreach ($entries as $entry) {
            $uniqueEntries[$entry['grapheme']] = $entry;
        }
        
        // Sort by grapheme
        ksort($uniqueEntries);
        
        // Generate XML content
        $xmlContent = $this->generateXml($uniqueEntries);
        
        // Write XML to file
        if (file_put_contents('agent/pronunciation-dictionary.xml', $xmlContent) === false) {
            $io->error('Could not write agent/pronunciation-dictionary.xml');
            return Command::FAILURE;
        }
        
        $io->text("✓ Generated " . count($uniqueEntries) . " pronunciation entries");
        $io->success('Pronunciation dictionary generated successfully!');
        
        return Command::SUCCESS;
    }
    
    private function extractPronunciationEntries(string $content): array
    {
        $entries = [];
        
        // Extract room numbers (W###)
        preg_match_all('/\b(W\d{3}(?:-W\d{3})?)\b/', $content, $matches);
        foreach ($matches[1] as $room) {
            $entries[] = [
                'grapheme' => $room,
                'alias' => $this->convertRoomToPronunciation($room)
            ];
        }
        
        // Extract level numbers (L#)
        preg_match_all('/\b(L\d)\b/', $content, $matches);
        foreach ($matches[1] as $level) {
            $entries[] = [
                'grapheme' => $level,
                'alias' => $this->convertLevelToPronunciation($level)
            ];
        }
        
        // Extract acronyms and special terms
        $specialTerms = [
            'LVCC' => 'las vegas convention center west hall',
            'IoT' => 'internet of things',
            'BBQ' => 'barbecue',
            'AFK' => 'A F K',
            'ATL' => 'Atlanta',
            'BIC' => 'Blacks in Cybersecurity',
            'LGBTQIA' => 'L G B T Q I A plus',
            'RFID' => 'R F I D',
            'RF' => 'R F',
            'DJ' => 'D J',
            'DJs' => 'D J\'s',
            'Vegemite' => 'vej uh mite',
            'Pai Sho' => 'pie show',
            'telephony' => 'tel ef oh nee',
            'transistors' => 'tran sis tors',
            'lockpicking' => 'lock picking',
            'mansplaining' => 'man splaining',
            'subculture' => 'sub culture',
            'unfreeze' => 'un freeze',
            'unfrozen' => 'un frozen',
            'weaponize' => 'weapon ize',
            'breakcore' => 'break core',
            'hardcore' => 'hard core',
            'hardstyle' => 'hard style',
            'speedcore' => 'speed core',
            'hip-hop' => 'hip hop',
            'onesies' => 'one zees',
            'phat' => 'fat',
            'scifi' => 'sci fi'
        ];
        
        foreach ($specialTerms as $term => $pronunciation) {
            if (stripos($content, $term) !== false) {
                $entries[] = [
                    'grapheme' => $term,
                    'alias' => $pronunciation
                ];
            }
        }
        
        // Extract DEF CON related terms
        if (stripos($content, 'DEF CON') !== false) {
            $entries[] = [
                'grapheme' => 'DEF CON',
                'alias' => 'def con'
            ];
        }
        
        if (stripos($content, 'DEFCON') !== false) {
            $entries[] = [
                'grapheme' => 'DEFCON',
                'alias' => 'def con'
            ];
        }
        
        // Extract DC groups
        preg_match_all('/\b(DC\d{3})\b/', $content, $matches);
        foreach ($matches[1] as $dcGroup) {
            $entries[] = [
                'grapheme' => $dcGroup,
                'alias' => $this->convertDCGroupToPronunciation($dcGroup)
            ];
        }
        
        return $entries;
    }
    
    private function convertRoomToPronunciation(string $room): string
    {
        if (strpos($room, '-') !== false) {
            // Handle ranges like W228-W229
            $parts = explode('-', $room);
            $first = $this->convertSingleRoom($parts[0]);
            $second = $this->convertSingleRoom($parts[1]);
            return $first . ' to ' . $second;
        }
        
        return $this->convertSingleRoom($room);
    }
    
    private function convertSingleRoom(string $room): string
    {
        $letter = substr($room, 0, 1);
        $number = substr($room, 1);
        
        $numberWords = $this->numberToWords($number);
        
        return $letter . ' ' . $numberWords;
    }
    
    private function convertLevelToPronunciation(string $level): string
    {
        $number = substr($level, 1);
        
        return 'Level ' . $this->numberToWords($number);
    }
    
    private function convertDCGroupToPronunciation(string $dcGroup): string
    {
        $number = substr($dcGroup, 2);
        return 'D C ' . $this->numberToWords($number);
    }
    
    private function numberToWords(string $number): string
    {
        $words = [
            '0' => 'zero', '1' => 'one', '2' => 'two', '3' => 'three', '4' => 'four',
            '5' => 'five', '6' => 'six', '7' => 'seven', '8' => 'eight', '9' => 'nine',
            '10' => 'ten', '11' => 'eleven', '12' => 'twelve', '13' => 'thirteen',
            '14' => 'fourteen', '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
            '18' => 'eighteen', '19' => 'nineteen', '20' => 'twenty', '21' => 'twenty one',
            '22' => 'twenty two', '23' => 'twenty three', '24' => 'twenty four',
            '25' => 'twenty five', '26' => 'twenty six', '27' => 'twenty seven',
            '28' => 'twenty eight', '29' => 'twenty nine', '30' => 'thirty',
            '31' => 'thirty one', '32' => 'thirty two', '33' => 'thirty three',
            '34' => 'thirty four', '35' => 'thirty five', '36' => 'thirty six',
            '37' => 'thirty seven', '38' => 'thirty eight', '39' => 'thirty nine',
            '40' => 'forty', '41' => 'forty one', '42' => 'forty two', '43' => 'forty three',
            '44' => 'forty four', '45' => 'forty five', '46' => 'forty six',
            '47' => 'forty seven', '48' => 'forty eight', '49' => 'forty nine',
            '50' => 'fifty', '51' => 'fifty one', '52' => 'fifty two', '53' => 'fifty three',
            '54' => 'fifty four', '55' => 'fifty five', '56' => 'fifty six',
            '57' => 'fifty seven', '58' => 'fifty eight', '59' => 'fifty nine',
            '60' => 'sixty', '61' => 'sixty one', '62' => 'sixty two', '63' => 'sixty three',
            '64' => 'sixty four', '65' => 'sixty five', '66' => 'sixty six',
            '67' => 'sixty seven', '68' => 'sixty eight', '69' => 'sixty nine',
            '70' => 'seventy', '71' => 'seventy one', '72' => 'seventy two',
            '73' => 'seventy three', '74' => 'seventy four', '75' => 'seventy five',
            '76' => 'seventy six', '77' => 'seventy seven', '78' => 'seventy eight',
            '79' => 'seventy nine', '80' => 'eighty', '81' => 'eighty one',
            '82' => 'eighty two', '83' => 'eighty three', '84' => 'eighty four',
            '85' => 'eighty five', '86' => 'eighty six', '87' => 'eighty seven',
            '88' => 'eighty eight', '89' => 'eighty nine', '90' => 'ninety',
            '91' => 'ninety one', '92' => 'ninety two', '93' => 'ninety three',
            '94' => 'ninety four', '95' => 'ninety five', '96' => 'ninety six',
            '97' => 'ninety seven', '98' => 'ninety eight', '99' => 'ninety nine'
        ];
        
        return $words[$number] ?? $number;
    }
    
    private function generateXml(array $entries): string
    {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<lexicon version=\"1.0\" xmlns=\"http://www.w3.org/2005/01/pronunciation-lexicon\" alphabet=\"ipa\">\n";
        
        foreach ($entries as $entry) {
            $xml .= "  <lexeme>\n";
            $xml .= "    <grapheme>" . htmlspecialchars($entry['grapheme']) . "</grapheme>\n";
            $xml .= "    <alias>" . htmlspecialchars($entry['alias']) . "</alias>\n";
            $xml .= "  </lexeme>\n";
        }
        
        $xml .= "</lexicon>";
        
        return $xml;
    }
} 