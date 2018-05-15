<?php namespace Uit\Bmenu\Models;

use Model;

/**
 * Settings Model
 */
class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'uit_bmenu_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';
}
