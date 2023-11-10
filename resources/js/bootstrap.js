import Swal from 'sweetalert2';
window.Swal = Swal;
import { Toast } from './utils/alert/Toast'
window.Toast = Toast;
import { 
    addFormValues, getFormValues, setFormValues, validateFormValuesPresent , previewSelectedImage 
} from './utils/form/functions';
window.formHelper = {
    addFormValues, getFormValues, setFormValues, validateFormValuesPresent, previewSelectedImage
}
import {
    openElementById, closeElementById
} from './utils/dom/functions'
window.domHelper = {
    openElementById, closeElementById
}
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

