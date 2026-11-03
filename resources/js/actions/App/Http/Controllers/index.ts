import DashboardController from './DashboardController'
import DonorController from './DonorController'
import DonorEventController from './DonorEventController'
import BloodStockController from './BloodStockController'
import HospitalRequestController from './HospitalRequestController'
import ReportNotificationController from './ReportNotificationController'
import Settings from './Settings'
const Controllers = {
    DashboardController: Object.assign(DashboardController, DashboardController),
DonorController: Object.assign(DonorController, DonorController),
DonorEventController: Object.assign(DonorEventController, DonorEventController),
BloodStockController: Object.assign(BloodStockController, BloodStockController),
HospitalRequestController: Object.assign(HospitalRequestController, HospitalRequestController),
ReportNotificationController: Object.assign(ReportNotificationController, ReportNotificationController),
Settings: Object.assign(Settings, Settings),
}

export default Controllers