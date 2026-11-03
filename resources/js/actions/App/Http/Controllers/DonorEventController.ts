import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DonorEventController::index
 * @see app/Http/Controllers/DonorEventController.php:19
 * @route '/events'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/events',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DonorEventController::index
 * @see app/Http/Controllers/DonorEventController.php:19
 * @route '/events'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DonorEventController::index
 * @see app/Http/Controllers/DonorEventController.php:19
 * @route '/events'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\DonorEventController::index
 * @see app/Http/Controllers/DonorEventController.php:19
 * @route '/events'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\DonorEventController::index
 * @see app/Http/Controllers/DonorEventController.php:19
 * @route '/events'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\DonorEventController::index
 * @see app/Http/Controllers/DonorEventController.php:19
 * @route '/events'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\DonorEventController::index
 * @see app/Http/Controllers/DonorEventController.php:19
 * @route '/events'
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
* @see \App\Http\Controllers\DonorEventController::store
 * @see app/Http/Controllers/DonorEventController.php:71
 * @route '/events'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/events',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DonorEventController::store
 * @see app/Http/Controllers/DonorEventController.php:71
 * @route '/events'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DonorEventController::store
 * @see app/Http/Controllers/DonorEventController.php:71
 * @route '/events'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\DonorEventController::store
 * @see app/Http/Controllers/DonorEventController.php:71
 * @route '/events'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\DonorEventController::store
 * @see app/Http/Controllers/DonorEventController.php:71
 * @route '/events'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
/**
* @see \App\Http\Controllers\DonorEventController::show
 * @see app/Http/Controllers/DonorEventController.php:56
 * @route '/events/{event}'
 */
export const show = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/events/{event}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DonorEventController::show
 * @see app/Http/Controllers/DonorEventController.php:56
 * @route '/events/{event}'
 */
show.url = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { event: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { event: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    event: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        event: typeof args.event === 'object'
                ? args.event.id
                : args.event,
                }

    return show.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DonorEventController::show
 * @see app/Http/Controllers/DonorEventController.php:56
 * @route '/events/{event}'
 */
show.get = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\DonorEventController::show
 * @see app/Http/Controllers/DonorEventController.php:56
 * @route '/events/{event}'
 */
show.head = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\DonorEventController::show
 * @see app/Http/Controllers/DonorEventController.php:56
 * @route '/events/{event}'
 */
    const showForm = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: show.url(args, options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\DonorEventController::show
 * @see app/Http/Controllers/DonorEventController.php:56
 * @route '/events/{event}'
 */
        showForm.get = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\DonorEventController::show
 * @see app/Http/Controllers/DonorEventController.php:56
 * @route '/events/{event}'
 */
        showForm.head = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: show.url(args, {
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    show.form = showForm
/**
* @see \App\Http\Controllers\DonorEventController::update
 * @see app/Http/Controllers/DonorEventController.php:99
 * @route '/events/{event}'
 */
export const update = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/events/{event}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\DonorEventController::update
 * @see app/Http/Controllers/DonorEventController.php:99
 * @route '/events/{event}'
 */
update.url = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { event: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { event: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    event: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        event: typeof args.event === 'object'
                ? args.event.id
                : args.event,
                }

    return update.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DonorEventController::update
 * @see app/Http/Controllers/DonorEventController.php:99
 * @route '/events/{event}'
 */
update.put = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

    /**
* @see \App\Http\Controllers\DonorEventController::update
 * @see app/Http/Controllers/DonorEventController.php:99
 * @route '/events/{event}'
 */
    const updateForm = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: update.url(args, {
                    [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                        _method: 'PUT',
                        ...(options?.query ?? options?.mergeQuery ?? {}),
                    }
                }),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\DonorEventController::update
 * @see app/Http/Controllers/DonorEventController.php:99
 * @route '/events/{event}'
 */
        updateForm.put = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\DonorEventController::addParticipant
 * @see app/Http/Controllers/DonorEventController.php:220
 * @route '/events/{event}/participants'
 */
export const addParticipant = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addParticipant.url(args, options),
    method: 'post',
})

addParticipant.definition = {
    methods: ["post"],
    url: '/events/{event}/participants',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DonorEventController::addParticipant
 * @see app/Http/Controllers/DonorEventController.php:220
 * @route '/events/{event}/participants'
 */
addParticipant.url = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { event: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { event: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    event: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        event: typeof args.event === 'object'
                ? args.event.id
                : args.event,
                }

    return addParticipant.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DonorEventController::addParticipant
 * @see app/Http/Controllers/DonorEventController.php:220
 * @route '/events/{event}/participants'
 */
addParticipant.post = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addParticipant.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\DonorEventController::addParticipant
 * @see app/Http/Controllers/DonorEventController.php:220
 * @route '/events/{event}/participants'
 */
    const addParticipantForm = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: addParticipant.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\DonorEventController::addParticipant
 * @see app/Http/Controllers/DonorEventController.php:220
 * @route '/events/{event}/participants'
 */
        addParticipantForm.post = (args: { event: number | { id: number } } | [event: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: addParticipant.url(args, options),
            method: 'post',
        })
    
    addParticipant.form = addParticipantForm
/**
* @see \App\Http\Controllers\DonorEventController::recordAttendance
 * @see app/Http/Controllers/DonorEventController.php:125
 * @route '/events/attendances/{attendance}'
 */
export const recordAttendance = (args: { attendance: number | { id: number } } | [attendance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: recordAttendance.url(args, options),
    method: 'post',
})

recordAttendance.definition = {
    methods: ["post"],
    url: '/events/attendances/{attendance}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DonorEventController::recordAttendance
 * @see app/Http/Controllers/DonorEventController.php:125
 * @route '/events/attendances/{attendance}'
 */
recordAttendance.url = (args: { attendance: number | { id: number } } | [attendance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { attendance: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { attendance: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    attendance: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        attendance: typeof args.attendance === 'object'
                ? args.attendance.id
                : args.attendance,
                }

    return recordAttendance.definition.url
            .replace('{attendance}', parsedArgs.attendance.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DonorEventController::recordAttendance
 * @see app/Http/Controllers/DonorEventController.php:125
 * @route '/events/attendances/{attendance}'
 */
recordAttendance.post = (args: { attendance: number | { id: number } } | [attendance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: recordAttendance.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\DonorEventController::recordAttendance
 * @see app/Http/Controllers/DonorEventController.php:125
 * @route '/events/attendances/{attendance}'
 */
    const recordAttendanceForm = (args: { attendance: number | { id: number } } | [attendance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: recordAttendance.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\DonorEventController::recordAttendance
 * @see app/Http/Controllers/DonorEventController.php:125
 * @route '/events/attendances/{attendance}'
 */
        recordAttendanceForm.post = (args: { attendance: number | { id: number } } | [attendance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: recordAttendance.url(args, options),
            method: 'post',
        })
    
    recordAttendance.form = recordAttendanceForm
const DonorEventController = { index, store, show, update, addParticipant, recordAttendance }

export default DonorEventController