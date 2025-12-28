<?php

namespace Hyvr\Rocket\Commands;

use Hyvr\Rocket\Core\RocketURLGenerator;
use Hyvr\Rocket\Helpers\ConfigHelper;
use Hyvr\Rocket\Helpers\FileHelper;
use Hyvr\Rocket\Helpers\MinifyHelper;
use Hyvr\Rocket\Helpers\TerminalHelper;
use Hyvr\Rocket\Helpers\URLHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use PhpZip\Exception\ZipException;
use PhpZip\ZipFile;
use Illuminate\Routing\UrlGenerator;

class BuildCommand extends Command
{
    protected $signature = 'rocket:build';
    protected $description = 'Generate the static build of the Laravel app';

    public function handle(){
        TerminalHelper::printHeader($this);

        ConfigHelper::init($this);

        config(['rocket.is_in_build_context' => true]);

        app()->extend(UrlGenerator::class, function ($url, $app){
            return new RocketURLGenerator(app()['router']->getRoutes(), app()['request']);
        });

        $this->line('<fg=yellow>🔥 Baking project build.</>');
        $this->newLine();

        $base_path = base_path();
        $build_path = $base_path.'/'.'build';
        $dist_path = $base_path.'/'.'dist';
        $public_path = $base_path.'/'.'public';

        if(File::exists($build_path)) File::deleteDirectory($build_path);
        File::makeDirectory($build_path);
        
        if(File::exists($dist_path)) File::deleteDirectory($dist_path);
        File::makeDirectory($dist_path);
        
        foreach(File::directories($public_path) as $folder){
            File::copyDirectory($folder, ($build_path.'/'.basename($folder)));
        }

        foreach(File::files($public_path) as $file){
            if($file->getFilename()!='index.php'){
                File::copy($file->getPathname(), $build_path.'/'.$file->getFilename());
            }
        }

        $this->line('⚙️ Rendering HTML files from views.');

        $routes = collect(Route::getRoutes())->filter(function ($route){
            return in_array('web', $route->gatherMiddleware());
        });

        $this->newLine();

        $bar = $this->output->createProgressBar(count($routes));

        foreach($routes as $route){
            $route_parameter_values = isset($route->routeParameterValues)?$route->routeParameterValues:[[]];

            foreach($route_parameter_values as $route_parameter_value){
                $route_uri = $route->uri();
                $route_uri = URLHelper::buildRouteWithParameter($route_uri, $route_parameter_value);

                $route_method = $route->methods()[0] ?? 'GET';
                $file_path = $build_path.'/'.$route_uri;

                $request = Request::create($route_uri, $route_method);
                $response = App::handle($request);
                $html = $response->getContent();

                if(!File::exists($file_path)) File::makeDirectory($file_path, 0755, true);

                if($html!=''){
                    File::append($file_path.'/index.html', $html);
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $files_map = FileHelper::map($build_path);

        if(config('rocket.minify.html', false)){
            $this->newLine();
            $this->line('⚙️ Minifing HTML files.');
            $this->newLine();
            
            $bar = $this->output->createProgressBar(count($routes));

            foreach($files_map['html'] as $path){
                $contents = File::get($path);
                $contents = MinifyHelper::html($contents);

                File::put($path, $contents);

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
        }

        if(config('rocket.minify.css', false)){
            $this->newLine();
            $this->line('⚙️ Minifing CSS files.');
            $this->newLine();
            
            $bar = $this->output->createProgressBar(count($routes));

            foreach($files_map['css'] as $path){
                $contents = File::get($path);
                $contents = MinifyHelper::css($contents);

                File::put($path, $contents);

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
        }

        if(config('rocket.minify.js', false)){
            $this->newLine();
            $this->line('⚙️ Minifing javascript files.');
            $this->newLine();
            
            $bar = $this->output->createProgressBar(count($routes));

            foreach($files_map['js'] as $path){
                $contents = File::get($path);
                $contents = MinifyHelper::js($contents);

                File::put($path, $contents);

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
        }

        if(config('rocket.distribution', true)){
            $this->newLine();
            $this->line('⚙️ Compressing the build to a zip file.');

            $zip_file = new ZipFile();
            try{
                $zip_file->addDirRecursive($build_path)
                    ->saveAsFile($dist_path.'/build-'.time().'.zip')
                    ->close();

                $this->newLine();
                $this->line('<fg=green>✅ Zipping the build is completed.</>');
            }
            catch(ZipException $exception){
                $this->newLine();
                $this->line('<fg=red>⛔️ Zipping method failed.</>');
            }
            finally{
                $zip_file->close();
            }
        }

        $this->newLine();
        $this->line('<fg=cyan>🌀 Build zip file is fully completed and available in the <options=bold>"dist"</> directory.</>');

        if(config('rocket.distribution', false)) $this->line('<fg=cyan>🌀 Zip file of the build is available in the <options=bold>"build"</> directory.</>');

        return Command::SUCCESS;
    }
}