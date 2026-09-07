<?php

namespace App\Filters;

use App\Models\MaintenanceSettingModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * When maintenance mode is enabled (toggled by a super admin in the CMS),
 * shows a 503 "site under maintenance" page to anonymous visitors while
 * anyone already logged into the CMS keeps browsing the public site
 * normally. The /cms/* area itself is excluded from this filter (see
 * Config/Filters.php), so logging in is always possible.
 */
class MaintenanceFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        try {
            $settings = (new MaintenanceSettingModel())->first();
        } catch (\Throwable) {
            return;
        }

        if ($settings === null || (int) $settings['is_enabled'] !== 1) {
            return;
        }

        if (auth()->loggedIn()) {
            return;
        }

        $message = trim((string) ($settings['message'] ?? ''));

        $response = service('response');
        $response->setStatusCode(503);
        $response->setHeader('Retry-After', '3600');
        $response->setHeader('X-Robots-Tag', 'noindex, nofollow');

        return $response->setBody(view('site/pages/maintenance', ['message' => $message]));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do.
    }
}
