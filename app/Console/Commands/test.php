<?php

namespace App\Console\Commands;

use App\Models\Package;
use App\Models\Tag;
use Illuminate\Console\Command;

class test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'packagist:search {search_type1} {search_value1} {search_type2?} {search_value2?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        ini_set("memory_limit", "-1");
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $search_types = ['q', 'tags', 'type'];
        $search_type1 = $this->argument('search_type1');
        $search_type2 = $this->argument('search_type2');
        $search_value1 = $this->argument('search_value1');
        $search_value2 = $this->argument('search_value2');

        $has_error = false;
        if (!in_array($search_type1, $search_types)) {
            $this->error('provided search_type: '.$search_type1.' is not one of valid values: '.implode(', ',
                    $search_types));
            $has_error = true;
        }
        if ($search_type2 && !in_array($search_type2, $search_types)) {
            $this->error('provided search_type: '.$search_type2.' is not one of valid values: '.implode(', ',
                    $search_types));
            $has_error = true;
        }
        if ($search_value2 != $search_type2 && (is_null($search_value2) || is_null($search_type2))) {
            $this->error('second search params are broken: '.$search_type2."=".$search_value2);
            $has_error = true;
        }
        if ($has_error) {
            return;
        }


        $url = "https://packagist.org/search.json?".$search_type1."=".$search_value1;
        if ($search_type2 && $search_value2) {
            $url .= "&".$search_type2."=".$search_value2;
        }
//        dump($url);

        $response = json_decode(file_get_contents($url));
        $count = min($response->total, 1000);
        $this->info("Found ".$response->total."(".$count.") packages using ".$url);
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        while (true) {
//            print_r($response);
            $count = Package::count();
            foreach ($response->results as $packageObj) {
                $packageArr = json_decode(json_encode($packageObj), true);
//                dump($packageArr);
                $package = Package::updateOrCreate(
                    ['name' => $packageArr['name']],
                    $packageArr
                );
                $tag = Tag::firstOrCreate(
                    ['name' => $search_value1]
                );
                if (!$package->hasTag($tag)) {
                    $package->tags()->attach($tag);
                }
                $tag = Tag::firstOrCreate(
                    ['name' => $search_value2]
                );
                if (!$package->hasTag($tag)) {
                    $package->tags()->attach($tag);
                }

                $bar->advance();
            }
            $countChange = Package::count() - $count;

            if (isset($response->next)) {
                $response = json_decode(file_get_contents($response->next));
            } else {
//                unset($response->results);
//                dump($response);
                break;
            }
            if ($countChange > 0) {
                sleep(5 + random_int(7, 13));
            }
        }

        $bar->finish();
        $this->info("\nsuccessfully done!");
    }
}
