import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\BloodStockController::index
 * @see app/Http/Controllers/BloodStockController.php:15
 * @route '/stocks'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/stocks',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BloodStockController::index
 * @see app/Http/Controllers/BloodStockController.php:15
 * @route '/stocks'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BloodStockController::index
 * @see app/Http/Controllers/BloodStockController.php:15
 * @route '/stocks'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BloodStockController::index
 * @see app/Http/Controllers/BloodStockController.php:15
 * @route '/stocks'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\BloodStockController::index
 * @see app/Http/Controllers/BloodStockController.php:15
 * @route '/stocks'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\BloodStockController::index
 * @see app/Http/Controllers/BloodStockController.php:15
 * @route '/stocks'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\BloodStockController::index
 * @see app/Http/Controllers/BloodStockController.php:15
 * @route '/stocks'
 */
        indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    index.form = indexForm
/**
* @see \App\Http\Controllers\BloodStockController::updateStock
 * @see app/Http/Controllers/BloodStockController.php:64
 * @route '/stocks/update'
 */
export const updateStock = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateStock.url(options),
    method: 'post',
})

updateStock.definition = {
    methods: ["post"],
    url: '/stocks/update',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BloodStockController::updateStock
 * @see app/Http/Controllers/BloodStockController.php:64
 * @route '/stocks/update'
 */
updateStock.url = (options?: RouteQueryOptions) => {
    return updateStock.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BloodStockController::updateStock
 * @see app/Http/Controllers/BloodStockController.php:64
 * @route '/stocks/update'
 */
updateStock.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateStock.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\BloodStockController::updateStock
 * @see app/Http/Controllers/BloodStockController.php:64
 * @route '/stocks/update'
 */
    const updateStockForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: updateStock.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\BloodStockController::updateStock
 * @see app/Http/Controllers/BloodStockController.php:64
 * @route '/stocks/update'
 */
        updateStockForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: updateStock.url(options),
            method: 'post',
        })
    
    updateStock.form = updateStockForm
const BloodStockController = { index, updateStock }

export default BloodStockController