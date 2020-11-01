<?php

namespace App\Console\Commands;

use App\Factories\DownloadMediaFactory;
use App\Media;
use App\Modules\PeriodsHelper;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ConvertVideosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert:videos {period?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will convert videos to audio files.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /**
         * no period set => using current month
         */

        $periodArgument = $this->argument('period') ? Carbon::createFromFormat('Y-m', $this->argument('period')) : Carbon::now();

        $period = PeriodsHelper::create($periodArgument->month, $periodArgument->year);

        /**
        * getting all non grabbed episodes published during this period order by (with channel and subscription)
        */
        $medias = Media::with('channel')->publishedBetween($period->startDate(), $period->endDate())->whereNotNull('grabbed_at')->get();

        if (!$medias->count()) {
            $this->comment('There is no ungrabbed medias and you should have a look.', 'v');
            return ;
        }

        /**
         * for every medias in db
         */
        foreach ($medias as $media) {
            DownloadMediaFactory::media($media, $this->getOutput()->isVerbose());
        }
    }

    public function defaultPeriod()
    {
        return date('Y') . '-' . date('n');
    }
}
