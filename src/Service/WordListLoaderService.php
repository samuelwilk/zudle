<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class WordListLoaderService
{
    public function __construct(private CacheInterface $cache, private ParameterBagInterface $parameterBag)
    {
    }

    public function loadWordList(): array
    {
        try {
            return $this->cache->get('word_list', function (ItemInterface $item) {
                $item->expiresAfter(3600); // Cache for 1 hour or any suitable duration

                // Load the JSON file
                $jsonFilePath = $this->parameterBag->get('kernel.project_dir').'/src/Data/words_dictionary.json';
                $json = file_get_contents($jsonFilePath);
                $words = json_decode($json, true);

                if (null === $words) {
                    throw new \RuntimeException('Failed to decode JSON from word list.');
                }

                return array_keys($words); // Use only keys for faster checks
            });
        } catch (\InvalidArgumentException $e) {
            // Log the error or handle it in a meaningful way
            // Optionally return an empty array or a fallback value
            error_log('Cache error: '.$e->getMessage());

            // Fallback to loading from file without caching
            return $this->loadWordListFromFile();
        }
    }

    private function loadWordListFromFile(): array
    {
        $jsonFilePath = $this->parameterBag->get('kernel.project_dir').'/src/Data/words_dictionary.json';
        $json = file_get_contents($jsonFilePath);
        $words = json_decode($json, true);

        if (null === $words) {
            throw new \RuntimeException('Failed to decode JSON from word list.');
        }

        return array_keys($words);
    }
}
