import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\HospitalRequestController::index
 * @see app/Http/Controllers/HospitalRequestController.php:16
 * @route '/requests'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/requests',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\HospitalRequestController::index
 * @see app/Http/Controllers/HospitalRequestController.php:16
 * @route '/requests'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\HospitalRequestController::index
 * @see app/Http/Controllers/HospitalRequestController.php:16
 * @route '/requests'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\HospitalRequestController::index
 * @see app/Http/Controllers/HospitalRequestController.php:16
 * @route '/requests'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\HospitalRequestController::index
 * @see app/Http/Controllers/HospitalRequestController.php:16
 * @route '/requests'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\HospitalRequestController::index
 * @see app/Http/Controllers/HospitalRequestController.php:16
 * @route '/requests'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\HospitalRequestController::index
 * @see app/Http/Controllers/HospitalRequestController.php:16
 * @route '/requests'
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
* @see \App\Http\Controllers\HospitalRequestController::store
 * @see app/Http/Controllers/HospitalRequestController.php:62
 * @route '/requests'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/requests',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\HospitalRequestController::store
 * @see app/Http/Controllers/HospitalRequestController.php:62
 * @route '/requests'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\HospitalRequestController::store
 * @see app/Http/Controllers/HospitalRequestController.php:62
 * @route '/requests'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\HospitalRequestController::store
 * @see app/Http/Controllers/HospitalRequestController.php:62
 * @route '/requests'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\HospitalRequestController::store
 * @see app/Http/Controllers/HospitalRequestController.php:62
 * @route '/requests'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\HospitalRequestController::approve
 * @see app/Http/Controllers/HospitalRequestController.php:92
 * @route '/requests/{hospitalRequest}/approve'
 */
export const approve = (args: { hospitalRequest: number | { id: number } } | [hospitalRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

approve.definition = {
    methods: ["post"],
    url: '/requests/{hospitalRequest}/approve',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\HospitalRequestController::approve
 * @see app/Http/Controllers/HospitalRequestController.php:92
 * @route '/requests/{hospitalRequest}/approve'
 */
approve.url = (args: { hospitalRequest: number | { id: number } } | [hospitalRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { hospitalRequest: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { hospitalRequest: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    hospitalRequest: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        hospitalRequest: typeof args.hospitalRequest === 'object'
                ? args.hospitalRequest.id
                : args.hospitalRequest,
                }

    return approve.definition.url
            .replace('{hospitalRequest}', parsedArgs.hospitalRequest.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\HospitalRequestController::approve
 * @see app/Http/Controllers/HospitalRequestController.php:92
 * @route '/requests/{hospitalRequest}/approve'
 */
approve.post = (args: { hospitalRequest: number | { id: number } } | [hospitalRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\HospitalRequestController::approve
 * @see app/Http/Controllers/HospitalRequestController.php:92
 * @route '/requests/{hospitalRequest}/approve'
 */
    const approveForm = (args: { hospitalRequest: number | { id: number } } | [hospitalRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: approve.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\HospitalRequestController::approve
 * @see app/Http/Controllers/HospitalRequestController.php:92
 * @route '/requests/{hospitalRequest}/approve'
 */
        approveForm.post = (args: { hospitalRequest: number | { id: number } } | [hospitalRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: approve.url(args, options),
            method: 'post',
        })
    
    approve.form = approveForm
/**
* @see \App\Http\Controllers\HospitalRequestController::reject
 * @see app/Http/Controllers/HospitalRequestController.php:141
 * @route '/requests/{hospitalRequest}/reject'
 */
export const reject = (args: { hospitalRequest: number | { id: number } } | [hospitalRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

reject.definition = {
    methods: ["post"],
    url: '/requests/{hospitalRequest}/reject',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\HospitalRequestController::reject
 * @see app/Http/Controllers/HospitalRequestController.php:141
 * @route '/requests/{hospitalRequest}/reject'
 */
reject.url = (args: { hospitalRequest: number | { id: number } } | [hospitalRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { hospitalRequest: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { hospitalRequest: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    hospitalRequest: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        hospitalRequest: typeof args.hospitalRequest === 'object'
                ? args.hospitalRequest.id
                : args.hospitalRequest,
                }

    return reject.definition.url
            .replace('{hospitalRequest}', parsedArgs.hospitalRequest.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\HospitalRequestController::reject
 * @see app/Http/Controllers/HospitalRequestController.php:141
 * @route '/requests/{hospitalRequest}/reject'
 */
reject.post = (args: { hospitalRequest: number | { id: number } } | [hospitalRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\HospitalRequestController::reject
 * @see app/Http/Controllers/HospitalRequestController.php:141
 * @route '/requests/{hospitalRequest}/reject'
 */
    const rejectForm = (args: { hospitalRequest: number | { id: number } } | [hospitalRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: reject.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\HospitalRequestController::reject
 * @see app/Http/Controllers/HospitalRequestController.php:141
 * @route '/requests/{hospitalRequest}/reject'
 */
        rejectForm.post = (args: { hospitalRequest: number | { id: number } } | [hospitalRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: reject.url(args, options),
            method: 'post',
        })
    
    reject.form = rejectForm
/**
* @see \App\Http\Controllers\HospitalRequestController::complete
 * @see app/Http/Controllers/HospitalRequestController.php:157
 * @route '/requests/{hospitalRequest}/complete'
 */
export const complete = (args: { hospitalRequest: number | { id: number } } | [hospitalRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(args, options),
    method: 'post',
})

complete.definition = {
    methods: ["post"],
    url: '/requests/{hospitalRequest}/complete',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\HospitalRequestController::complete
 * @see app/Http/Controllers/HospitalRequestController.php:157
 * @route '/requests/{hospitalRequest}/complete'
 */
complete.url = (args: { hospitalRequest: number | { id: number } } | [hospitalRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { hospitalRequest: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { hospitalRequest: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    hospitalRequest: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        hospitalRequest: typeof args.hospitalRequest === 'object'
                ? args.hospitalRequest.id
                : args.hospitalRequest,
                }

    return complete.definition.url
            .replace('{hospitalRequest}', parsedArgs.hospitalRequest.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\HospitalRequestController::complete
 * @see app/Http/Controllers/HospitalRequestController.php:157
 * @route '/requests/{hospitalRequest}/complete'
 */
complete.post = (args: { hospitalRequest: number | { id: number } } | [hospitalRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\HospitalRequestController::complete
 * @see app/Http/Controllers/HospitalRequestController.php:157
 * @route '/requests/{hospitalRequest}/complete'
 */
    const completeForm = (args: { hospitalRequest: number | { id: number } } | [hospitalRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: complete.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\HospitalRequestController::complete
 * @see app/Http/Controllers/HospitalRequestController.php:157
 * @route '/requests/{hospitalRequest}/complete'
 */
        completeForm.post = (args: { hospitalRequest: number | { id: number } } | [hospitalRequest: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: complete.url(args, options),
            method: 'post',
        })
    
    complete.form = completeForm
const requests = {
    index: Object.assign(index, index),
store: Object.assign(store, store),
approve: Object.assign(approve, approve),
reject: Object.assign(reject, reject),
complete: Object.assign(complete, complete),
}

export default requests