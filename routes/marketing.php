<?php

declare(strict_types=1);

use App\Http\Controllers\Marketing\CloudflarePurgeMarketingController;
use App\Http\Controllers\Marketing\Company\Handbook;
use App\Http\Controllers\Marketing\Docs;
use App\Http\Controllers\Marketing\Features;
use App\Http\Controllers\Marketing\MarketingController;
use Illuminate\Support\Facades\Route;

Route::get('/infra/cloudflare/purge-marketing', CloudflarePurgeMarketingController::class);

Route::middleware(['marketing'])->group(function (): void {

    Route::get('/', [MarketingController::class, 'index'])->name('marketing.index');
    Route::get('/docs', [Docs\DocController::class, 'index'])->name('marketing.docs.index');

    // docs about modules
    Route::get('/docs/modules', [Docs\Modules\ModulesController::class, 'index'])->name('marketing.docs.modules');

    // api docs
    Route::get('/docs/api', [Docs\Api\ApiIntroductionController::class, 'index'])->name('marketing.docs.api.index');
    Route::get('/docs/api/authentication', [Docs\Api\AuthenticationController::class, 'index'])->name('marketing.docs.api.authentication');
    Route::get('/docs/api/profile', [Docs\Api\ProfileController::class, 'index'])->name('marketing.docs.api.account.profile');
    Route::get('/docs/api/api-management', [Docs\Api\ApiManagementController::class, 'index'])->name('marketing.docs.api.account.api-management');
    Route::get('/docs/api/logs', [Docs\Api\LogController::class, 'index'])->name('marketing.docs.api.account.logs');
    Route::get('/docs/api/emails', [Docs\Api\EmailSentController::class, 'index'])->name('marketing.docs.api.account.emails');
    Route::get('/docs/api/account', [Docs\Api\AccountController::class, 'index'])->name('marketing.docs.api.account');
    Route::get('/docs/api/journals', [Docs\Api\JournalController::class, 'index'])->name('marketing.docs.api.journals');
    Route::get('/docs/api/journal-entries', [Docs\Api\JournalEntryController::class, 'index'])->name('marketing.docs.api.journal-entries');
    Route::get('/docs/api/modules/cognitive-load', [Docs\Api\Modules\CognitiveLoadController::class, 'index'])->name('marketing.docs.api.modules.cognitive-load');
    Route::get('/docs/api/modules/day-type', [Docs\Api\Modules\DayTypeController::class, 'index'])->name('marketing.docs.api.modules.day-type');
    Route::get('/docs/api/modules/energy', [Docs\Api\Modules\EnergyController::class, 'index'])->name('marketing.docs.api.modules.energy');
    Route::get('/docs/api/modules/health', [Docs\Api\Modules\HealthController::class, 'index'])->name('marketing.docs.api.modules.health');
    Route::get('/docs/api/modules/hygiene', [Docs\Api\Modules\HygieneController::class, 'index'])->name('marketing.docs.api.modules.hygiene');
    Route::get('/docs/api/modules/kids', [Docs\Api\Modules\KidsController::class, 'index'])->name('marketing.docs.api.modules.kids');
    Route::get('/docs/api/modules/mood', [Docs\Api\Modules\MoodController::class, 'index'])->name('marketing.docs.api.modules.mood');
    Route::get('/docs/api/modules/meals', [Docs\Api\Modules\MealsController::class, 'index'])->name('marketing.docs.api.modules.meals');
    Route::get('/docs/api/modules/shopping', [Docs\Api\Modules\ShoppingController::class, 'index'])->name('marketing.docs.api.modules.shopping');
    Route::get('/docs/api/modules/social-density', [Docs\Api\Modules\SocialDensityController::class, 'index'])->name('marketing.docs.api.modules.social-density');
    Route::get('/docs/api/modules/physical-activity', [Docs\Api\Modules\PhysicalActivityController::class, 'index'])->name('marketing.docs.api.modules.physical-activity');
    Route::get('/docs/api/modules/primary-obligation', [Docs\Api\Modules\PrimaryObligationController::class, 'index'])->name('marketing.docs.api.modules.primary-obligation');
    Route::get('/docs/api/modules/sexual-activity', [Docs\Api\Modules\SexualActivityController::class, 'index'])->name('marketing.docs.api.modules.sexual-activity');
    Route::get('/docs/api/modules/sleep', [Docs\Api\Modules\SleepController::class, 'index'])->name('marketing.docs.api.modules.sleep');
    Route::get('/docs/api/modules/travel', [Docs\Api\Modules\TravelController::class, 'index'])->name('marketing.docs.api.modules.travel');
    Route::get('/docs/api/modules/weather', [Docs\Api\Modules\WeatherController::class, 'index'])->name('marketing.docs.api.modules.weather');
    Route::get('/docs/api/modules/weather-influence', [Docs\Api\Modules\WeatherInfluenceController::class, 'index'])->name('marketing.docs.api.modules.weather-influence');
    Route::get('/docs/api/modules/work', [Docs\Api\Modules\WorkController::class, 'index'])->name('marketing.docs.api.modules.work');

    // features
    Route::get('/features/modules', [Features\FeaturesController::class, 'index'])->name('marketing.features.modules');

    // company
    Route::get('/company/handbook', [Handbook\HandbookController::class, 'index'])->name('marketing.company.handbook.index');
    Route::get('/company/handbook/project', [Handbook\HandbookProjectController::class, 'index'])->name('marketing.company.handbook.project');
    Route::get('/company/handbook/principles', [Handbook\HandbookPrinciplesController::class, 'index'])->name('marketing.company.handbook.principles');
    Route::get('/company/handbook/shipping', [Handbook\HandbookShippingController::class, 'index'])->name('marketing.company.handbook.shipping');
    Route::get('/company/handbook/money', [Handbook\HandbookMoneyController::class, 'index'])->name('marketing.company.handbook.money');
    Route::get('/company/handbook/why-open-source', [Handbook\HandbookWhyOpenSourceController::class, 'index'])->name('marketing.company.handbook.why-open-source');
    Route::get('/company/handbook/where-am-I-going-with-this', [Handbook\HandbookWhereController::class, 'index'])->name('marketing.company.handbook.where');
    Route::get('/company/handbook/marketing', [Handbook\HandbookMarketingController::class, 'index'])->name('marketing.company.handbook.marketing.envision');
    Route::get('/company/handbook/social-media', [Handbook\HandbookSocialMediaController::class, 'index'])->name('marketing.company.handbook.marketing.social-media');
    Route::get('/company/handbook/writing', [Handbook\HandbookWritingController::class, 'index'])->name('marketing.company.handbook.marketing.writing');
    Route::get('/company/handbook/product-philosophy', [Handbook\HandbookProductPhilosophyController::class, 'index'])->name('marketing.company.handbook.marketing.product-philosophy');
    Route::get('/company/handbook/prioritize', [Handbook\HandbookPrioritizeController::class, 'index'])->name('marketing.company.handbook.marketing.prioritize');
});
