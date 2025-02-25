<?php

namespace Grav\Plugin;

use Composer\Autoload\ClassLoader;
use Grav\Common\Grav;
use Grav\Common\Page\Interfaces\PageInterface;
use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class PlanComparePlugin
 * @package Grav\Plugin
 */
class PlanComparePlugin extends Plugin
{
    /** @var array */
    public $features = [
        'blueprints' => 0, // Use priority 0
    ];

    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onPluginsInitialized' => [
                ['onPluginsInitialized', 0]
            ],
            'onTwigInitialized' => ['onTwigInitialized', 0],
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
        ];
    }

    /**
     * Composer autoload
     *
     * @return ClassLoader
     */
    public function autoload(): ClassLoader
    {
        return require __DIR__ . '/vendor/autoload.php';
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized(): void
    {
        if ($this->isAdmin()) {
            $this->enable([
                'onAdminSave' => ['onAdminSave', 0],
                'onGetPageTemplates' => ['onGetPageTemplates', 0],
                'onGetPageBlueprints' => ['onGetPageBlueprints', 0],
            ]);

            return;
        }

        $this->enable([
            'onAssetsInitialized' => ['onAssetsInitialized', 0],
        ]);
    }

    /**
     * @param $event
     */
    public function onAdminSave($event)
    {
        $page = $event['object'];

        if (!$page instanceof PageInterface) {
            return;
        }

        $header = $page->header();

        if (!isset($header["plancompare"])) {
            return;
        }

        $data = $header["plancompare"] ?? [];

        foreach ($data['features'] as $feature) {
            if (!empty($feature['divider'])) {
                continue;
            }

            foreach ($data['plans'] as $plan) {
                $slug_relation = sprintf('%s|%s', $this->slugify($feature['label']), $this->slugify($plan['label']));

                if (empty($header['table'][$slug_relation])) {
                    continue;
                }

                $header->offsetSet(sprintf('plancompare.table.%s', $slug_relation), $header['table'][$slug_relation]);
            }
        }
    }

    /**
     * Add blueprint directory to page templates.
     */
    public function onGetPageTemplates(Event $event)
    {
        $locator = Grav::instance()['locator'];
        $event->types->scanTemplates($locator->findResource(sprintf('plugin://%s/templates', $this->name)));
    }

    /**
     * Extend page blueprints with additional configuration options.
     *
     * @param Event $event
     */
    public function onGetPageBlueprints($event)
    {
        $locator = Grav::instance()['locator'];
        $event->types->scanBlueprints($locator->findResource(sprintf('plugin://%s/blueprints', $this->name)));
    }

    /**
     * Register templates
     *
     * @return void
     */
    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = sprintf('plugin://%s/templates', $this->name);
    }

    /**
     * @param Event $event
     */
    public function onTwigInitialized(Event $event)
    {
        $this->grav['twig']->twig()->addFilter(
            new \Twig_SimpleFilter('slugify', [$this, 'slugify'])
        );
    }

    /**
     * @param Event $event
     */
    public function onAssetsInitialized(Event $event)
    {
        if ($this->config->get(sprintf('plugins.%s.built_in_css', $this->name))) {
            $this->grav['assets']->addCss(sprintf('plugin://%1$s/assets/css/%1$s.css', $this->name));
        }
    }

    public function slugify($text, string $divider = '-')
    {
        // replace non letter or digits by divider
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $divider);

        // remove duplicate divider
        $text = preg_replace('~-+~', $divider, $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}
