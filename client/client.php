<?php

namespace WPU\Client;

class Client {

    /**
     * API URL for handling update information
     *
     * @var string
     */
    const API_URL = 'http://update.domain.com/api/1.0/endpoint';

    /**
     * Theme/plugin slug
     *
     * @var string
     */
    private $slug;

    /**
     * Theme/plugin slug
     *
     * @var string
     */
    private $type;

    /**
     * Plugin path
     *
     * @var string|null
     */
    private $path;

    /**
     * Construct the update instance
     *
     * @param string $type
     * @param string $slug
     * @param string|null $path
     */
    public function __construct($type, $slug, $path = null) {
        $this->type = $type;
        $this->slug = $slug;
        $this->path = $path;
    }

    /**
     * Creates and registers a new update instance
     *
     * @param string $type
     * @param string $slug
     * @param string|null $path
     */
    public static function add_update_check($type, $slug, $path = null) {

        $instance = new Client($type, $slug, $path);

        if ($type == 'theme') {

            add_filter('pre_set_site_transient_update_themes', array(&$instance, 'check_theme_update'));

        } elseif ($type == 'plugin') {

            add_filter('pre_set_site_transient_update_plugins', array(&$instance, 'check_plugin_update'));
            add_filter('plugins_api', array(&$instance, 'plugin_api_call'), 10, 3);

        }

    }

    /**
     * Check for plugin updates
     *
     * @param stdClass $transient
     *
     * @return mixed
     */
    public function check_plugin_update($transient) {

        if (empty($transient->checked)) {
            return $transient;
        }

        $request_args = array(
            'plugin' => $this->path,
            'slug' => $this->slug,
            'version' => $transient->checked[ $this->path ]
        );

        $request_string = $this->prepare_request('plugin_update', $request_args);
        $raw_response = wp_remote_post(self::API_URL, $request_string);
        $response = null;

        if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200)) {
            $response = unserialize($raw_response['body']);
        }

        if (is_object($response) && !empty($response)) {
            $transient->response[ $this->path ] = $response;
        }

        return $transient;
    }

    /**
     * Check for theme updates
     *
     * @param stdClass $transient
     *
     * @return mixed
     */
    public function check_theme_update($transient) {

        if (empty($transient->checked)) {
            return $transient;
        }

        $request_args = array(
            'slug' => $this->slug,
            'version' => $transient->checked[ $this->slug ]
        );

        $request_string = $this->prepare_request('theme_update', $request_args);
        $raw_response = wp_remote_post(self::API_URL, $request_string);

        $response = null;

        if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200)) {
            $response = unserialize($raw_response['body']);
        }

        if (!empty($response)) {
            $transient->response[ $this->slug ] = $response;
        }

        return $transient;
    }

    /**
     * Custom API call for getting plugin information
     *
     * @param $def
     * @param $action
     * @param $args
     *
     * @return mixed|WP_Error
     */
    public function plugin_api_call($def, $action, $args) {

        if (!isset($args->slug) || $args->slug != $this->slug) {
            return $def;
        }

        $plugin_info = get_site_transient('update_plugins');

        $request_args = array(
            'slug' => $this->slug,
            'version' => (isset($plugin_info->checked[ $this->path ])) ? $plugin_info->checked[ $this->path ] : 0
        );

        $request_string = $this->prepare_request($action, $request_args);
        $raw_response = wp_remote_post(self::API_URL, $request_string);

        if (is_wp_error($raw_response)) {
            $res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.', 'codex_maven'), $raw_response->get_error_message());
        } else {
            $res = unserialize($raw_response['body']);
            if ($res === false) {
                $res = new WP_Error('plugins_api_failed', __('An unknown error occurred', 'codex_maven'), $raw_response['body']);
            }
        }

        return $res;
    }

    /**
     * Prepare API request
     *
     * @param string $action
     * @param array $args
     *
     * @return array
     */
    private function prepare_request($action, $args) {

        global $wp_version;

        return array(
            'body' => array(
                'action' => $action,
                'request' => serialize($args),
                'api-key' => md5(home_url())
            ),
            'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url()
        );
    }

}