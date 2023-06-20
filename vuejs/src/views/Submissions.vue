<script setup>

function generate_submission_rows($entries_array) {
    let submissions = array.reduce((obj, item) => {
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
</script>

<template>

</template>