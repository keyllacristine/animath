<?php
class AudioManager
{
    private array $sounds = [
        'background' => '/assets/audio/background.mp3',
        'correct' => '/assets/audio/correct.mp3',
        'incorrect' => '/assets/audio/incorrect.mp3',
        'levelComplete' => '/assets/audio/level-complete.mp3',
        'levelUnlock' => '/assets/audio/level-unlock.mp3',
        'click' => '/assets/audio/click.mp3',
    ];

    private array $audioSettings = [
        'background' => ['volume' => 0.5, 'loop' => true],
        'correct' => ['volume' => 0.8, 'loop' => false],
        'incorrect' => ['volume' => 0.7, 'loop' => false],
        'levelComplete' => ['volume' => 0.9, 'loop' => false],
        'levelUnlock' => ['volume' => 0.8, 'loop' => false],
        'click' => ['volume' => 0.6, 'loop' => false],
    ];

    public function getAudioSettings(): array
    {
        return $this->audioSettings;
    }

    public function getSounds(): array
    {
        return $this->sounds;
    }

    public function getAudioPath(string $soundKey): ?string
    {
        return $this->sounds[$soundKey] ?? null;
    }
}