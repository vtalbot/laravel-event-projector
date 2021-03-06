<?php

namespace Spatie\EventProjector\Snapshots;

use Illuminate\Support\Collection;
use Spatie\EventProjector\EventProjectionist;
use Illuminate\Contracts\Filesystem\Filesystem;

class SnapshotRepository
{
    /** @var array */
    protected $config;

    /** @var \Illuminate\Contracts\Filesystem\Filesystem */
    protected $disk;

    /** @var \Spatie\EventProjector\EventProjectionist */
    protected $eventProjectionist;

    public function __construct(array $config, Filesystem $disk, EventProjectionist $eventProjectionist)
    {
        $this->config = $config;

        $this->disk = $disk;

        $this->eventProjectionist = $eventProjectionist;
    }

    public function get(): Collection
    {
        return collect($this->disk->allFiles())
            ->map(function (string $fileName) {
                return new Snapshot($this->eventProjectionist, $this->config, $this->disk, $fileName);
            })
            ->filter->isValid()
            ->sortByDesc(function (Snapshot $snapshot) {
                return $snapshot->createdAt()->format('timestamp');
            })
            ->values();
    }
}
