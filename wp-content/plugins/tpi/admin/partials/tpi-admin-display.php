<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="tpi-wrapper">
    <div>
        <div class="meta discount_range">
            <div class="meta-th">
                <span class="discount_range_title">Discount Range</span>
            </div>
        </div>
        <!--
        <div class="meta-row">
            <div class="meta-th">
                <label for="discount_range_id" class="tpi-row-title">Discount Range</label>
            </div>
            <div class="meta-td">
                <input type="text" name="discount_range_id" id="discount_range_id" value=""/>
            </div>
        </div>
        -->

        <label for="lowest_discount" class="tpi_discount_title">from</label>
        <input type="text" name="lowest_discount" id="lowest_discount" class="tpi_discount" value=""/>
        <label for="lowest_discount" class="tpi_discount_suffix">%</label>

        <label for="highest_discount" class="tpi_discount_title">to</label>
        <input type="text" name="highest_discount" id="highest_discount" class="tpi_discount" value=""/>
        <label for="highest_discount" class="tpi_discount_suffix">%</label>

        <!--

        <div class="meta-row">
            <div class="meta-th">
                <label for="lowest_discount" class="tpi-row-title">from</label>
            </div>
            <div class="meta-td">
                <input type="text" name="lowest_discount" id="lowest_discount" value=""/>
            </div>
        </div>
        <div class="meta-row">
            <div class="meta-th">
                <label for="highest_discount" class="tpi-row-title">to</label>
            </div>
            <div class="meta-td">
                <input type="text" name="highest_discount" id="highest_discount" value=""/>
            </div>
        </div>
        -->

        <div class="meta-editor"></div>
    </div>
</div>