import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DonorController::index
 * @see app/Http/Controllers/DonorController.php:13
 * @route '/donors'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/donors',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DonorController::index
 * @see app/Http/Controllers/DonorController.php:13
 * @route '/donors'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DonorController::index
 * @see app/Http/Controllers/DonorController.php:13
 * @route '/donors'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\DonorController::index
 * @see app/Http/Controllers/DonorController.php:13
 * @route '/donors'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\DonorController::index
 * @see app/Http/Controllers/DonorController.php:13
 * @route '/donors'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\DonorController::index
 * @see app/Http/Controllers/DonorController.php:13
 * @route '/donors'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\DonorController::index
 * @see app/Http/Controllers/DonorController.php:13
 * @route '/donors'
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
* @see \App\Http\Controllers\DonorController::store
 * @see app/Http/Controllers/DonorController.php:51
 * @route '/donors'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/donors',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DonorController::store
 * @see app/Http/Controllers/DonorController.php:51
 * @route '/donors'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DonorController::store
 * @see app/Http/Controllers/DonorController.php:51
 * @route '/donors'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\DonorController::store
 * @see app/Http/Controllers/DonorController.php:51
 * @route '/donors'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\DonorController::store
 * @see app/Http/Controllers/DonorController.php:51
 * @route '/donors'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\DonorController::update
 * @see app/Http/Controllers/DonorController.php:82
 * @route '/donors/{donor}'
 */
export const update = (args: { donor: number | { id: number } } | [donor: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/donors/{donor}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\DonorController::update
 * @see app/Http/Controllers/DonorController.php:82
 * @route '/donors/{donor}'
 */
update.url = (args: { donor: number | { id: number } } | [donor: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { donor: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { donor: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    donor: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        donor: typeof args.donor === 'object'
                ? args.donor.id
                : args.donor,
                }

    return update.definition.url
            .replace('{donor}', parsedArgs.donor.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DonorController::update
 * @see app/Http/Controllers/DonorController.php:82
 * @route '/donors/{donor}'
 */
update.put = (args: { donor: number | { id: number } } | [donor: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\DonorController::update
 * @see app/Http/Controllers/DonorController.php:82
 * @route '/donors/{donor}'
 */
    const updateForm = (args: { donor: number | { id: number } } | [donor: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\DonorController::update
 * @see app/Http/Controllers/DonorController.php:82
 * @route '/donors/{donor}'
 */
        updateForm.put = (args: { donor: number | { id: number } } | [donor: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: update.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'PUT',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    update.form = updateForm
/**
* @see \App\Http\Controllers\DonorController::verify
 * @see app/Http/Controllers/DonorController.php:109
 * @route '/donors/{donor}/verify'
 */
export const verify = (args: { donor: number | { id: number } } | [donor: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: verify.url(args, options),
    method: 'post',
})

verify.definition = {
    methods: ["post"],
    url: '/donors/{donor}/verify',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DonorController::verify
 * @see app/Http/Controllers/DonorController.php:109
 * @route '/donors/{donor}/verify'
 */
verify.url = (args: { donor: number | { id: number } } | [donor: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { donor: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { donor: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    donor: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        donor: typeof args.donor === 'object'
                ? args.donor.id
                : args.donor,
                }

    return verify.definition.url
            .replace('{donor}', parsedArgs.donor.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DonorController::verify
 * @see app/Http/Controllers/DonorController.php:109
 * @route '/donors/{donor}/verify'
 */
verify.post = (args: { donor: number | { id: number } } | [donor: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: verify.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\DonorController::verify
 * @see app/Http/Controllers/DonorController.php:109
 * @route '/donors/{donor}/verify'
 */
    const verifyForm = (args: { donor: number | { id: number } } | [donor: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: verify.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\DonorController::verify
 * @see app/Http/Controllers/DonorController.php:109
 * @route '/donors/{donor}/verify'
 */
        verifyForm.post = (args: { donor: number | { id: number } } | [donor: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: verify.url(args, options),
            method: 'post',
        })
    
    verify.form = verifyForm
/**
* @see \App\Http\Controllers\DonorController::destroy
 * @see app/Http/Controllers/DonorController.php:147
 * @route '/donors/{donor}'
 */
export const destroy = (args: { donor: number | { id: number } } | [donor: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/donors/{donor}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\DonorController::destroy
 * @see app/Http/Controllers/DonorController.php:147
 * @route '/donors/{donor}'
 */
destroy.url = (args: { donor: number | { id: number } } | [donor: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { donor: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { donor: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    donor: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        donor: typeof args.donor === 'object'
                ? args.donor.id
                : args.donor,
                }

    return destroy.definition.url
            .replace('{donor}', parsedArgs.donor.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DonorController::destroy
 * @see app/Http/Controllers/DonorController.php:147
 * @route '/donors/{donor}'
 */
destroy.delete = (args: { donor: number | { id: number } } | [donor: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

    /**
* @see \App\Http\Controllers\DonorController::destroy
 * @see app/Http/Controllers/DonorController.php:147
 * @route '/donors/{donor}'
 */
    const destroyForm = (args: { donor: number | { id: number } } | [donor: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: destroy.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'DELETE',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\DonorController::destroy
 * @see app/Http/Controllers/DonorController.php:147
 * @route '/donors/{donor}'
 */
        destroyForm.delete = (args: { donor: number | { id: number } } | [donor: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: destroy.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'DELETE',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'post',
        })
    
    destroy.form = destroyForm
const DonorController = { index, store, update, verify, destroy }

export default DonorController