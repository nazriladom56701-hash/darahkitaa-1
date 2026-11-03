import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ReportNotificationController::index
 * @see app/Http/Controllers/ReportNotificationController.php:19
 * @route '/reports'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/reports',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportNotificationController::index
 * @see app/Http/Controllers/ReportNotificationController.php:19
 * @route '/reports'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportNotificationController::index
 * @see app/Http/Controllers/ReportNotificationController.php:19
 * @route '/reports'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ReportNotificationController::index
 * @see app/Http/Controllers/ReportNotificationController.php:19
 * @route '/reports'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ReportNotificationController::index
 * @see app/Http/Controllers/ReportNotificationController.php:19
 * @route '/reports'
 */
    const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: index.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ReportNotificationController::index
 * @see app/Http/Controllers/ReportNotificationController.php:19
 * @route '/reports'
 */
        indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: index.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ReportNotificationController::index
 * @see app/Http/Controllers/ReportNotificationController.php:19
 * @route '/reports'
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
* @see \App\Http\Controllers\ReportNotificationController::sendReminder
 * @see app/Http/Controllers/ReportNotificationController.php:61
 * @route '/reports/send-reminder'
 */
export const sendReminder = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendReminder.url(options),
    method: 'post',
})

sendReminder.definition = {
    methods: ["post"],
    url: '/reports/send-reminder',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ReportNotificationController::sendReminder
 * @see app/Http/Controllers/ReportNotificationController.php:61
 * @route '/reports/send-reminder'
 */
sendReminder.url = (options?: RouteQueryOptions) => {
    return sendReminder.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportNotificationController::sendReminder
 * @see app/Http/Controllers/ReportNotificationController.php:61
 * @route '/reports/send-reminder'
 */
sendReminder.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendReminder.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\ReportNotificationController::sendReminder
 * @see app/Http/Controllers/ReportNotificationController.php:61
 * @route '/reports/send-reminder'
 */
    const sendReminderForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: sendReminder.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ReportNotificationController::sendReminder
 * @see app/Http/Controllers/ReportNotificationController.php:61
 * @route '/reports/send-reminder'
 */
        sendReminderForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: sendReminder.url(options),
            method: 'post',
        })
    
    sendReminder.form = sendReminderForm
/**
* @see \App\Http\Controllers\ReportNotificationController::broadcastReminder
 * @see app/Http/Controllers/ReportNotificationController.php:106
 * @route '/reports/broadcast'
 */
export const broadcastReminder = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: broadcastReminder.url(options),
    method: 'post',
})

broadcastReminder.definition = {
    methods: ["post"],
    url: '/reports/broadcast',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ReportNotificationController::broadcastReminder
 * @see app/Http/Controllers/ReportNotificationController.php:106
 * @route '/reports/broadcast'
 */
broadcastReminder.url = (options?: RouteQueryOptions) => {
    return broadcastReminder.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportNotificationController::broadcastReminder
 * @see app/Http/Controllers/ReportNotificationController.php:106
 * @route '/reports/broadcast'
 */
broadcastReminder.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: broadcastReminder.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\ReportNotificationController::broadcastReminder
 * @see app/Http/Controllers/ReportNotificationController.php:106
 * @route '/reports/broadcast'
 */
    const broadcastReminderForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: broadcastReminder.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ReportNotificationController::broadcastReminder
 * @see app/Http/Controllers/ReportNotificationController.php:106
 * @route '/reports/broadcast'
 */
        broadcastReminderForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: broadcastReminder.url(options),
            method: 'post',
        })
    
    broadcastReminder.form = broadcastReminderForm
/**
* @see \App\Http\Controllers\ReportNotificationController::exportCsv
 * @see app/Http/Controllers/ReportNotificationController.php:149
 * @route '/reports/export-csv'
 */
export const exportCsv = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportCsv.url(options),
    method: 'get',
})

exportCsv.definition = {
    methods: ["get","head"],
    url: '/reports/export-csv',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportNotificationController::exportCsv
 * @see app/Http/Controllers/ReportNotificationController.php:149
 * @route '/reports/export-csv'
 */
exportCsv.url = (options?: RouteQueryOptions) => {
    return exportCsv.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportNotificationController::exportCsv
 * @see app/Http/Controllers/ReportNotificationController.php:149
 * @route '/reports/export-csv'
 */
exportCsv.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportCsv.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ReportNotificationController::exportCsv
 * @see app/Http/Controllers/ReportNotificationController.php:149
 * @route '/reports/export-csv'
 */
exportCsv.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportCsv.url(options),
    method: 'head',
})

    /**
* @see \App\Http\Controllers\ReportNotificationController::exportCsv
 * @see app/Http/Controllers/ReportNotificationController.php:149
 * @route '/reports/export-csv'
 */
    const exportCsvForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
        action: exportCsv.url(options),
        method: 'get',
    })

            /**
* @see \App\Http\Controllers\ReportNotificationController::exportCsv
 * @see app/Http/Controllers/ReportNotificationController.php:149
 * @route '/reports/export-csv'
 */
        exportCsvForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: exportCsv.url(options),
            method: 'get',
        })
            /**
* @see \App\Http\Controllers\ReportNotificationController::exportCsv
 * @see app/Http/Controllers/ReportNotificationController.php:149
 * @route '/reports/export-csv'
 */
        exportCsvForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
            action: exportCsv.url({
                        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
                            _method: 'HEAD',
                            ...(options?.query ?? options?.mergeQuery ?? {}),
                        }
                    }),
            method: 'get',
        })
    
    exportCsv.form = exportCsvForm
const ReportNotificationController = { index, sendReminder, broadcastReminder, exportCsv }

export default ReportNotificationController