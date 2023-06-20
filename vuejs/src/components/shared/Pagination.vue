<script setup>
import { ref } from "vue";

const props = defineProps({
  total_pages: Number,
  current_page: Number,
  per_page: Number,
});
let item_per_page = ref(props.per_page);

const emit = defineEmits(["perPageChange"]);

function pageChange(perpage) {
  emit("perPageChange", parseInt(perpage));
}
</script>

<template>
  <div class="d-flex mt-5 justify-content-between flex-wrap">
    <nav aria-label="..." v-if="total_pages > 0">
      <ul class="pagination">
        <!-- prev nav -->
        <li class="page-item" :class="current_page == 1 ? 'disabled' : ''">
          <button
            class="page-link"
            @click="$emit('pageChange', current_page - 1)"
            :disabled="current_page == 1"
          >
            Prev
          </button>
        </li>
        <!-- first page -->
        <li class="page-item" @click="$emit('pageChange', 1)" v-if="current_page != 1">
          <button class="page-link me-1">{{ 1 }}</button>
        </li>
        <!-- current page - 1 -->
        <li
          class="page-item"
          @click="$emit('pageChange', current_page - 1)"
          v-if="current_page - 1 >= 2"
        >
          <button class="page-link">{{ current_page - 1 }}</button>
        </li>
        <!-- current page -->
        <li class="page-item active" @click="$emit('pageChange', current_page)">
          <button disabled class="page-link">{{ current_page }}</button>
        </li>
        <!-- current page + 1 -->
        <li
          class="page-item"
          @click="$emit('pageChange', current_page + 1)"
          v-if="current_page + 1 <= total_pages - 1"
        >
          <button class="page-link">{{ current_page + 1 }}</button>
        </li>
        <!-- last page -->
        <li
          class="page-item"
          @click="$emit('pageChange', total_pages)"
          v-if="current_page != total_pages"
        >
          <button class="page-link ms-1">{{ total_pages }}</button>
        </li>
        <!-- next nav -->
        <li class="page-item" :class="current_page == total_pages ? 'disabled' : ''">
          <button
            class="page-link"
            @click="$emit('pageChange', current_page + 1)"
            :disabled="current_page == total_pages"
          >
            Next
          </button>
        </li>
      </ul>
    </nav>

    <div class="ms-auto select_per_page">
      <select
        v-model="item_per_page"
        @change="pageChange(item_per_page)"
        class="form-select form-select-sm"
        aria-label="form-select-sm example"
      >
        <option value="5">5 per page</option>
        <option value="10">10 per page</option>
        <option value="20">20 per page</option>
        <option value="30">30 per page</option>
        <option value="40">40 per page</option>
        <option value="50">50 per page</option>
      </select>
    </div>
  </div>
</template>

<style>
li.disabled.page-item {
  cursor: not-allowed !important;
}
</style>