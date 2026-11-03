import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
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
* @see \App\Http\Controllers\BloodStockController::update
 * @see app/Http/Controllers/BloodStockController.php:64
 * @route '/stocks/update'
 */
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/stocks/update',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BloodStockController::update
 * @see app/Http/Controllers/BloodStockController.php:64
 * @route '/stocks/update'
 */
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BloodStockController::update
 * @see app/Http/Controllers/BloodStockController.php:64
 * @route '/stocks/update'
 */
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\BloodStockController::update
 * @see app/Http/Controllers/BloodStockController.php:64
 * @route '/stocks/update'
 */
    const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\BloodStockController::update
 * @see app/Http/Controllers/BloodStockController.php:64
 * @route '/stocks/update'
 */
        updateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(options),
            method: 'post',
        })
    
    update.form = updateForm
const stocks = {
    index: Object.assign(index, index),
update: Object.assign(update, update),
}

export default stocks