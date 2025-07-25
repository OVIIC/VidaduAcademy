<?php
    use Filament\Support\Enums\Alignment;
    use Filament\Tables\Actions\HeaderActionsPosition;
?>

<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'actions' => [],
    'actionsPosition',
    'description' => null,
    'heading' => null,
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'actions' => [],
    'actionsPosition',
    'description' => null,
    'heading' => null,
]); ?>
<?php foreach (array_filter(([
    'actions' => [],
    'actionsPosition',
    'description' => null,
    'heading' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div
    <?php echo e($attributes->class([
            'fi-ta-header flex flex-col gap-3 p-4 sm:px-6',
            'sm:flex-row sm:items-center' => $actionsPosition === HeaderActionsPosition::Adaptive,
        ])); ?>

>
    <!--[if BLOCK]><![endif]--><?php if($heading || $description): ?>
        <div class="grid gap-y-1">
            <!--[if BLOCK]><![endif]--><?php if($heading): ?>
                <h3
                    class="fi-ta-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white"
                >
                    <?php echo e($heading); ?>

                </h3>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <!--[if BLOCK]><![endif]--><?php if($description): ?>
                <p
                    class="fi-ta-header-description text-sm text-gray-600 dark:text-gray-400"
                >
                    <?php echo e($description); ?>

                </p>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!--[if BLOCK]><![endif]--><?php if($actions): ?>
        <?php if (isset($component)) { $__componentOriginal32a2358b99de73a2a27625c392d6fe38 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal32a2358b99de73a2a27625c392d6fe38 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-tables::components.actions','data' => ['actions' => $actions,'alignment' => Alignment::Start,'wrap' => true,'class' => \Illuminate\Support\Arr::toCssClasses([
                'ms-auto' => $actionsPosition === HeaderActionsPosition::Adaptive && ! ($heading || $description),
                'sm:ms-auto' => $actionsPosition === HeaderActionsPosition::Adaptive,
            ])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament-tables::actions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($actions),'alignment' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(Alignment::Start),'wrap' => true,'class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(\Illuminate\Support\Arr::toCssClasses([
                'ms-auto' => $actionsPosition === HeaderActionsPosition::Adaptive && ! ($heading || $description),
                'sm:ms-auto' => $actionsPosition === HeaderActionsPosition::Adaptive,
            ]))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal32a2358b99de73a2a27625c392d6fe38)): ?>
<?php $attributes = $__attributesOriginal32a2358b99de73a2a27625c392d6fe38; ?>
<?php unset($__attributesOriginal32a2358b99de73a2a27625c392d6fe38); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal32a2358b99de73a2a27625c392d6fe38)): ?>
<?php $component = $__componentOriginal32a2358b99de73a2a27625c392d6fe38; ?>
<?php unset($__componentOriginal32a2358b99de73a2a27625c392d6fe38); ?>
<?php endif; ?>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH /Users/hermes/Documents/GitHub/VidaduAcademy/backend/vendor/filament/tables/src/../resources/views/components/header.blade.php ENDPATH**/ ?>