<?php

declare(strict_types=1);

namespace Tests\Unit\Factories;

use App\Exceptions\AssembleOutputFileMissingException;
use App\Factories\AssembleAudioFilesFactory;
use ArgumentCountError;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */

/**
 * @internal
 *
 * @coversNothing
 */
class AssembleAudioFilesFactoryTest extends TestCase
{
    use RefreshDatabase;

    protected string $introFile;
    protected string $outroFile;
    protected string $mediaFile;
    protected string $outputFile;

    public function setUp(): void
    {
        parent::setUp();
        $this->introFile = fixtures_path('Audio/l8i4O7_btaA.mp3'); // zelda
        $this->outroFile = fixtures_path('Audio/qfx6yf8pux4.mp3'); // mario coin
        $this->mediaFile = fixtures_path('Audio/lU_c9mku8JU.mp3'); // jean viet
        $this->outputFile = base_path('tmp/combined.mp3');
    }

    public function tearDown(): void
    {
        if (file_exists($this->outputFile)) {
            unlink($this->outputFile);
        }
        parent::tearDown();
    }

    /** @test */
    public function assemble_nothing_should_fail(): void
    {
        $this->expectException(ArgumentCountError::class);
        AssembleAudioFilesFactory::files();
    }

    /** @test */
    public function assemble_one_file_only_should_fail(): void
    {
        $this->expectException(ArgumentCountError::class);
        AssembleAudioFilesFactory::files($this->introFile);
    }

    /** @test */
    public function assemble_two_files_without_output_file_command_should_fail(): void
    {
        $this->expectException(AssembleOutputFileMissingException::class);
        AssembleAudioFilesFactory::files($this->introFile, $this->mediaFile)->command();
    }

    /** @test */
    public function assemble_two_files_with_output_command_should_be_good(): void
    {
        $expectedCommand = "ffmpeg -v 8 -y -i 'concat:" . $this->introFile . '|' . $this->mediaFile . "' -c:a libmp3lame " . $this->outputFile;
        $this->assertEquals(
            $expectedCommand,
            AssembleAudioFilesFactory::files($this->introFile, $this->mediaFile)->outputFile($this->outputFile)->command()
        );
    }

    /** @test */
    public function assemble_more_files_with_output_command_should_be_good(): void
    {
        $expectedCommand = "ffmpeg -v 8 -y -i 'concat:" . $this->introFile . '|' . $this->mediaFile . '|' . $this->outroFile . "' -c:a libmp3lame " . $this->outputFile;
        $this->assertEquals(
            $expectedCommand,
            AssembleAudioFilesFactory::files($this->introFile, $this->mediaFile, $this->outroFile)->outputFile($this->outputFile)->command()
        );
    }

    /** @test */
    public function assemble_two_files_should_be_good(): void
    {
        $this->assertTrue(
            AssembleAudioFilesFactory::files($this->introFile, $this->mediaFile)
                ->outputFile($this->outputFile)
                ->assemble()
        );
    }

    /** @test */
    public function assemble_more_files_should_be_good(): void
    {
        $this->assertTrue(
            AssembleAudioFilesFactory::files($this->introFile, $this->mediaFile, $this->outroFile)
                ->outputFile($this->outputFile)
                ->assemble()
        );
    }
}
