<?php

namespace App\Providers;

use App\Constants\ModuleEvent;
use App\Contracts;
use App\Contracts\ActivatorInterface;
use App\Exception\InvalidActivatorClass;
use App\Facades\Module;
use App\NwidartCommands;
use App\Service\Laravel;
use App\Service\ModuleManifest;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Translation\Translator;
use Symfony\Component\Console\Output\NullOutput;

class NwidartCommandsProvider extends ServiceProvider
{
    protected array $commands = [];

    /**
     * Booting the package.
     */
    public function boot()
    {

        if (! $this->app->runningInConsole()) {
            return;
        }

        $enableCommands = (bool) config('modules.enable_commands');

        if ($enableCommands) {

            $this->registerCommands();

            App::getInstance()->singleton(
                ModuleManifest::class,
                fn () => new ModuleManifest(
                    new Filesystem,
                    app(Contracts\RepositoryInterface::class)->getScanPaths(),
                    $this->getCachedModulePath(),
                    app(ActivatorInterface::class)
                )
            );

            $this->registerEvents();
        }

    }

    /**
     * Register the service provider.
     */
    public function register()
    {

        if (! $this->app->runningInConsole()) {
            return;
        }

        $enableCommands = (bool) config('modules.enable_commands');

        if ($enableCommands) {
            $this->registerServices();
            $this->registerProviders();
            $this->registerMigrations();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function registerServices()
    {

        App::getInstance()->singleton(Contracts\RepositoryInterface::class, function ($app) {
            $path = $app['config']->get('modules.paths.modules');

            return new Laravel\LaravelFileRepository($app, $path);
        });

        App::getInstance()->singleton(Contracts\ActivatorInterface::class, function ($app) {
            $activator = $app['config']->get('modules.activator');
            $class = $app['config']->get('modules.activators.'.$activator)['class'];

            if ($class === null) {
                throw InvalidActivatorClass::missingConfig();
            }

            return new $class($app);
        });

        App::getInstance()->alias(Contracts\RepositoryInterface::class, 'modules');
    }

    protected function registerMigrations(): void
    {
        if (! App::getInstance()['config']->get('modules.auto-discover.migrations', true)) {
            return;
        }

        App::getInstance()->resolving(Migrator::class, function (Migrator $migrator) {
            $migration_path = App::getInstance()['config']->get('modules.paths.generator.migration.path');
            collect(Module::allEnabled())
                ->each(function (Laravel\Module $module) use ($migration_path, $migrator) {
                    $migrator->path($module->getExtraPath($migration_path));
                });
        });
    }

    protected function registerTranslations(): void
    {
        if (! App::getInstance()['config']->get('modules.auto-discover.translations', true)) {
            return;
        }
        $this->callAfterResolving('translator', function (TranslatorContract $translator) {
            if (! $translator instanceof Translator) {
                return;
            }

            collect(Module::allEnabled())
                ->each(function (Laravel\Module $module) use ($translator) {
                    $path = $module->getExtraPath(App::getInstance()['config']->get('modules.paths.generator.lang.path'));
                    $translator->addNamespace($module->getLowerName(), $path);
                    $translator->addJsonPath($path);
                });
        });
    }

    private function registerEvents(): void
    {
        Event::listen(
            [
                'modules.*.'.ModuleEvent::DELETED,
                'modules.*.'.ModuleEvent::CREATED,
                'modules.*.'.ModuleEvent::DISABLED,
                'modules.*.'.ModuleEvent::ENABLED,
            ],
            fn () => Artisan::call('module:clear-compiled', outputBuffer: new NullOutput)
        );
    }

    public function provides(): array
    {
        return [Contracts\RepositoryInterface::class, 'modules'];
    }

    /**
     * Register providers.
     */
    protected function registerProviders()
    {
        App::getInstance()->register(ContractsServiceProvider::class);
    }

    protected function getCachedModulePath()
    {
        return Str::replaceLast('services.php', 'modules.php', App::getInstance()->getCachedServicesPath());
    }

    private function registerCommands()
    {
        $this->commands([
            NwidartCommands\Actions\CheckLangCommand::class,
            NwidartCommands\Actions\DisableCommand::class,
            NwidartCommands\Actions\DumpCommand::class,
            NwidartCommands\Actions\EnableCommand::class,
            NwidartCommands\Actions\InstallCommand::class,
            NwidartCommands\Actions\ListCommand::class,
            NwidartCommands\Actions\ModelPruneCommand::class,
            NwidartCommands\Actions\ModelShowCommand::class,
            NwidartCommands\Actions\ModuleDeleteCommand::class,
            NwidartCommands\Actions\UnUseCommand::class,
            NwidartCommands\Actions\UpdateCommand::class,
            NwidartCommands\Actions\UseCommand::class,

            // Database Commands
            NwidartCommands\Database\MigrateCommand::class,
            NwidartCommands\Database\MigrateRefreshCommand::class,
            NwidartCommands\Database\MigrateResetCommand::class,
            NwidartCommands\Database\MigrateRollbackCommand::class,
            NwidartCommands\Database\MigrateStatusCommand::class,
            NwidartCommands\Database\SeedCommand::class,

            // Make Commands
            NwidartCommands\Make\ActionMakeCommand::class,
            NwidartCommands\Make\CastMakeCommand::class,
            NwidartCommands\Make\ChannelMakeCommand::class,
            NwidartCommands\Make\ClassMakeCommand::class,
            NwidartCommands\Make\CommandMakeCommand::class,
            NwidartCommands\Make\ComponentClassMakeCommand::class,
            NwidartCommands\Make\ComponentViewMakeCommand::class,
            NwidartCommands\Make\ControllerMakeCommand::class,
            NwidartCommands\Make\EventMakeCommand::class,
            NwidartCommands\Make\EventProviderMakeCommand::class,
            NwidartCommands\Make\EnumMakeCommand::class,
            NwidartCommands\Make\ExceptionMakeCommand::class,
            NwidartCommands\Make\FactoryMakeCommand::class,
            NwidartCommands\Make\InterfaceMakeCommand::class,
            NwidartCommands\Make\HelperMakeCommand::class,
            NwidartCommands\Make\JobMakeCommand::class,
            NwidartCommands\Make\ListenerMakeCommand::class,
            NwidartCommands\Make\MailMakeCommand::class,
            NwidartCommands\Make\MiddlewareMakeCommand::class,
            NwidartCommands\Make\MigrationMakeCommand::class,
            NwidartCommands\Make\ModelMakeCommand::class,
            NwidartCommands\Make\ModuleMakeCommand::class,
            NwidartCommands\Make\NotificationMakeCommand::class,
            NwidartCommands\Make\ObserverMakeCommand::class,
            NwidartCommands\Make\PolicyMakeCommand::class,
            NwidartCommands\Make\ProviderMakeCommand::class,
            NwidartCommands\Make\RepositoryMakeCommand::class,
            NwidartCommands\Make\RequestMakeCommand::class,
            NwidartCommands\Make\ResourceMakeCommand::class,
            NwidartCommands\Make\RouteProviderMakeCommand::class,
            NwidartCommands\Make\RuleMakeCommand::class,
            NwidartCommands\Make\ScopeMakeCommand::class,
            NwidartCommands\Make\SeedMakeCommand::class,
            NwidartCommands\Make\ServiceMakeCommand::class,
            NwidartCommands\Make\TraitMakeCommand::class,
            NwidartCommands\Make\TestMakeCommand::class,
            NwidartCommands\Make\ViewMakeCommand::class,

            // Publish Commands
            NwidartCommands\Publish\PublishCommand::class,
            NwidartCommands\Publish\PublishConfigurationCommand::class,
            NwidartCommands\Publish\PublishMigrationCommand::class,
            NwidartCommands\Publish\PublishTranslationCommand::class,

            // Other Commands
            NwidartCommands\ComposerUpdateCommand::class,
            NwidartCommands\LaravelModulesV6Migrator::class,
            NwidartCommands\ModuleDiscoverCommand::class,
            NwidartCommands\ModuleClearCompiledCommand::class,
            NwidartCommands\SetupCommand::class,
            NwidartCommands\UpdatePhpunitCoverage::class,

            NwidartCommands\Database\MigrateFreshCommand::class,

        ]);
    }
}
