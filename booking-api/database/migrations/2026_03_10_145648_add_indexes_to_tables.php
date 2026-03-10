<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bookings — colonnes très filtrées
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['resource_id', 'status', 'payment_status']);
            $table->index(['user_id', 'status']);
            $table->index('check_in_date');
            $table->index('check_out_date');
            $table->index('payment_intent_id');
        });

        // Resources — filtre actif + catégorie
        Schema::table('resources', function (Blueprint $table) {
            $table->index(['is_active', 'category_id']);
            $table->index('location');
            $table->index('price_per_night');
        });

        // Availabilities — date + resource
        Schema::table('availabilities', function (Blueprint $table) {
            $table->index(['resource_id', 'is_available', 'date']);
        });

        // Resource images
        Schema::table('resource_images', function (Blueprint $table) {
            $table->index(['resource_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['resource_id', 'status', 'payment_status']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['check_in_date']);
            $table->dropIndex(['check_out_date']);
            $table->dropIndex(['payment_intent_id']);
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'category_id']);
            $table->dropIndex(['location']);
            $table->dropIndex(['price_per_night']);
        });

        Schema::table('availabilities', function (Blueprint $table) {
            $table->dropIndex(['resource_id', 'is_available', 'date']);
        });

        Schema::table('resource_images', function (Blueprint $table) {
            $table->dropIndex(['resource_id', 'is_primary']);
        });
    }
};
