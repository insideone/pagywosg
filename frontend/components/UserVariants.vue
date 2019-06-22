<template>
    <div class="variants">
        <div
            v-for="(user, key) in users"
            :key="'user_variant_'+key"
            class="variants__item"
            @click="userSelected(user)"
        >
            <div class="variants__item-pic-block variants__item-pic-block--user">
                <img
                    :src="user.avatar"
                    :alt="user.profileName"
                    class="variants__item-img"
                />
            </div>
            <div class="variants__item-body">
                <div class="variants__item-name">{{user.profileName}}</div>
                <a
                    :href="user.profileUrl"
                    target="_blank"
                    class="variants__item-link"
                >{{user.profileUrl}}</a>
            </div>
        </div>
    </div>
</template>

<script>
    import debounce from '../services/debounce';
    import { mapActions } from 'vuex'

    export default {
        name: "UserVariants",
        props: {
            searchString: {
                type: String,
                default: ''
            },
        },
        data: function () {
            return {
                users: []
            };
        },
        watch: {
            searchString: function () {
                this.handleSearchStringChange();
            }
        },
        methods: {
            ...mapActions(['fetchUsers']),

            handleSearchStringChange() {

                if (this.searchString === '')
                {
                    this.users = [];
                    return;
                }

                debounce.debounce(() => {

                    if (this.searchString === '')
                    {
                        this.users = [];
                        return;
                    }

                    this.fetchUsers(this.searchString).then(({errors, users}) => {
                        if (errors) {
                            this.users = [];
                            return;
                        }

                        this.users = users;
                    });
                }, 500)();
            },

            userSelected(user) {
                this.$emit('user-selected', user);
            }

        }
    }
</script>

<style lang="less">
    @import "../assets/blocks/variants";
</style>