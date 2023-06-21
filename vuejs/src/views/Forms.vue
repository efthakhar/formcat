<script setup>
import { onMounted, reactive, ref } from 'vue'
import axios from 'axios'

import Loader from "../components/shared/Loader.vue";
import Pagination from "../components/shared/Pagination.vue"
import router from '../router';

const forms = ref([]);
const loading = ref(false)
const current_page = ref(1)
const total_pages = ref(1)
const per_page = ref(10)

async function fetchForms(page = current_page.value, perpage = per_page.value) {
  loading.value = true
  await axios
    .get(
      `/wp-json/formcat/v1/forms?page=${page}&perpage=${perpage}`,
      {
        headers: {
          'content-type': 'application/json',
          'X-WP-Nonce': formcat.nonce
        }
      }
    )
    .then((response) => {
      forms.value = response.data.data;
      current_page.value = response.data.current_page
      total_pages.value = response.data.last_page
    })
    .catch((errors) => {
      //console.log(errors);
    });
  loading.value = false
}

onMounted(() => {
  fetchForms();
});

</script>
<template>
  <div class="formcat-forms-page p-3">
    <h3 class="h3 text-dark">Form List</h3>
    <div class="formcat-forms-page__table_container">
      <Loader v-if="loading == true" />
      <div class="table-responsive mt-3 data_table " v-if="loading == false">
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <!-- <th scope="col" style="width: 30px">
                <input type="checkbox" />
              </th> -->
              <th scope="col" class="minwidth-200">Form Name</th>
              <th scope="col" class="minwidth-100">Form Plugin</th>
              <th scope="col" class="minwidth-100">action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="form in forms" :key="form.id">
              <!-- <th scope="row">
                <input type="checkbox" />
              </th> -->
              <td>{{ form.form_name }}</td>
              <td>{{ form.plugin_name }}</td>
              <td>
                <button class="btn btn-sm btn-primary"
                  @click="router.push({ name: 'submissions', params: { form_id: form.id } })">submissions</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- padination and per page -->
      <Pagination v-if="loading == false && forms.length > 0"
        @pageChange="(currentPage) => fetchForms(currentPage, per_page)"
        @perPageChange="(perpage) => fetchForms(1, perpage)" :total_pages="total_pages" :current_page="current_page"
        :per_page="per_page" />
    </div>
  </div>
</template>

<style></style>
