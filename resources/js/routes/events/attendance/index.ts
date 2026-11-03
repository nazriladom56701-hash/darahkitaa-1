import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\DonorEventController::record
 * @see app/Http/Controllers/DonorEventController.php:125
 * @route '/events/attendances/{attendance}'
 */
export const record = (args: { attendance: number | { id: number } } | [attendance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: record.url(args, options),
    method: 'post',
})

record.definition = {
    methods: ["post"],
    url: '/events/attendances/{attendance}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DonorEventController::record
 * @see app/Http/Controllers/DonorEventController.php:125
 * @route '/events/attendances/{attendance}'
 */
record.url = (args: { attendance: number | { id: number } } | [attendance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return record.definition.url
            .replace('{attendance}', parsedArgs.attendance.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DonorEventController::record
 * @see app/Http/Controllers/DonorEventController.php:125
 * @route '/events/attendances/{attendance}'
 */
record.post = (args: { attendance: number | { id: number } } | [attendance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: record.url(args, options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\DonorEventController::record
 * @see app/Http/Controllers/DonorEventController.php:125
 * @route '/events/attendances/{attendance}'
 */
    const recordForm = (args: { attendance: number | { id: number } } | [attendance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: record.url(args, options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\DonorEventController::record
 * @see app/Http/Controllers/DonorEventController.php:125
 * @route '/events/attendances/{attendance}'
 */
        recordForm.post = (args: { attendance: number | { id: number } } | [attendance: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: record.url(args, options),
            method: 'post',
        })
    
    record.form = recordForm
const attendance = {
    record: Object.assign(record, record),
}

export default attendance