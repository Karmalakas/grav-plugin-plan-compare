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
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
            'onAssetsInitialized' => ['onAssetsInitialized', 0],
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
        if (!$this->isAdmin()) {
            return;
        }

        $this->enable([
            'onAdminSave' => ['onAdminSave', 0],
            'onGetPageTemplates' => ['onGetPageTemplates', 0],
            'onGetPageBlueprints' => ['onGetPageBlueprints', 0],
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
        $save_slugs = [];

        foreach ($data['features'] as $feature) {
            if (!empty($feature['divider'])) {
                continue;
            }

            foreach ($data['plans'] as $plan) {
                $save_slugs[sprintf('%s|%s', $feature['slug'], $plan['slug'])] = true;
            }
        }

        $header->set('plancompare.table', array_intersect_key($data['table'] ?? [], $save_slugs));
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
    public function onAssetsInitialized(Event $event)
    {
        if ($this->isAdmin()) {
            $this->grav['assets']->addCss(sprintf('plugin://%s/assets/css/admin.css', $this->name));
            $this->grav['assets']->addJs(sprintf('plugin://%s/assets/js/editor-observer.js', $this->name));
            $this->grav['assets']->addJs(sprintf('plugin://%s/assets/js/slugify.js', $this->name));

            return;
        }

        if ($this->config->get(sprintf('plugins.%s.built_in_css', $this->name))) {
            $this->grav['assets']->addCss(sprintf('plugin://%1$s/assets/css/%1$s.css', $this->name));
        }
    }

    /**
     * Get list of form field types specified in this plugin. Only special types needs to be listed.
     *
     * @return array
     */
    public function getFormFieldTypes()
    {
        return [
            'plan-compare' => ['array' => true],
        ];
    }
}
