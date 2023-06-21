<script setup>
import { useRoute } from 'vue-router';
import { onMounted, reactive, ref } from 'vue'
import axios from 'axios'

import Loader from "../components/shared/Loader.vue";
import Pagination from "../components/shared/Pagination.vue"

const route = useRoute();
const submissions = ref([]);
const submission_ids = ref([])
const loading = ref(false)
const current_page = ref(1)
const visible_fields = ref([])
const fields_alias = ref([])
const form_id = ref(route.params.form_id)
const total_pages = ref(1)
const per_page = ref(10)




async function fetchSubmissions(form_id, page = current_page.value, perpage = per_page.value) {

    loading.value = true
    if (form_id == null) {
        loading.value = false
        return
    }
    await axios
        .get(
            `/wp-json/formcat/v1/submissions?form_id=${form_id}&page=${page}&perpage=${perpage}`,
            {
                headers: {
                    'content-type': 'application/json',
                    'X-WP-Nonce': formcat.nonce
                }
            }
        )
        .then((response) => {
            
            console.log(response.data)
            
            submissions.value = generate_submission_rows(response.data.entries);
            current_page.value = response.data.current_page
            total_pages.value = response.data.last_page
            visible_fields.value = response.data.fields_visible_in_datatable
            fields_alias.value = response.data.fields_alias
            submission_ids.value = response.data.submission_ids

            
        })
        .catch((errors) => {
            //console.log(errors);
        });
    loading.value = false
}

function generate_submission_rows(entries_array) {
    let submissions = entries_array.reduce((obj, item) => {
        const { submission_id, field, value, id } = item;

        if (!obj.hasOwnProperty(submission_id)) {
            obj[submission_id] = {};
        }

        obj[submission_id]['id'] = id;
        obj[submission_id][field] = value;

        return obj;
    }, {});
    return submissions;
}

onMounted(() => {
    fetchSubmissions(form_id.value, current_page.value, per_page.value);
});


</script>

<template>
    <div class="formcat-forms-page p-3">
      <h3 class="h3 text-dark">Form Submissions</h3>
      <div class="formcat-forms-page__table_container">
        <Loader v-if="loading == true" />
        <div class="table-responsive mt-3 data_table " v-if="loading == false">
          <table class="table table-hover table-bordered">
            <thead>
              <tr >
                <!-- <th scope="col" style="width: 30px">
                  <input type="checkbox" />
                </th> -->
                <th scope="col" class="minwidth-200" v-for="field in visible_fields" :key="field">
                    {{ fields_alias[field] }} 
                </th>
                <th scope="col" class="minwidth-100">action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="submission in submissions" :key="submission.id">
                <td v-for="field in visible_fields">
                    {{ submission[field] }}
                </td>
                <td>
                    <button>action</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- padination and per page -->
        <Pagination v-if="loading == false && submission_ids.length > 0"
          @pageChange="(currentPage) => fetchSubmissions(form_id,currentPage, per_page)"
          @perPageChange="(perpage) => fetchSubmissions(form_id, 1, perpage)" :total_pages="total_pages" :current_page="current_page"
          :per_page="per_page" />
      </div>
    </div>
  </template>