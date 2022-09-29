<?php

namespace Maatwebsite\Sidebar\Presentation\Illuminate;

use Illuminate\Contracts\View\Factory;
use Maatwebsite\Sidebar\Presentation\ActiveStateChecker;
use Maatwebsite\Sidebar\Presentation\SidebarRenderer;
use Maatwebsite\Sidebar\Sidebar;

class IlluminateSidebarRenderer implements SidebarRenderer
{
    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var string
     */
    protected $view = 'sidebar::menu';

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function render(Sidebar $sidebar)
    {
        $menu = $sidebar->getMenu();

        if ($menu->isAuthorized()) {
            $groups = [];
            $i = 0;

            foreach ($menu->getGroups() as $key => $group) {
                if ($group->isAuthorized()) {
                    $groups[$i]['name'] = $group->getName();
                    $groups[$i]['header'] = true;
                    $groups[$i]['items'] = $this->generateItem($group->getItems());
                    ++$i;
                }
            }

            return $groups;
        }
    }

    private function generateItem($items)
    {
        $i = 0;
        $g_item = [];
        foreach ($items as $ikey => $item) {
            if ($item->isAuthorized()) {
                $g_item[$i]['name'] = $item->getName();
                $g_item[$i]['icon'] = $item->getIcon();
                $g_item[$i]['href'] = $item->getUrl();
                $g_item[$i]['class'] = $item->getItemClass();
                $g_item[$i]['badge'] = $item->getBadges();
                $g_item[$i]['new_tab'] = $item->getNewTab();
                $g_item[$i]['active'] = (new ActiveStateChecker())->isActive($item);
                if ($item->hasItems()) {
                    $g_item[$i]['items'] = $this->generateItem($item->getItems());
                }
                ++$i;
            }
        }

        return $g_item;
    }
}
