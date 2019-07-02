<template>
    <div class="users-page">

        <h1 class="heading">Users Roles Management</h1>

        <messagebox
            v-if="isLoading"
            mode="loading"
        />

        <div
            v-else
            class="text"
        >
            <messagebox
                v-for="(error, key) in errors"
                :key="'users_error_'+key"
                mode="error"
            >{{error.message}}</messagebox>

            <table v-if="users.length">
                <tr>
                    <th class="users-page__table-user-col">User</th>
                    <th
                        v-for="(role, key) in roles"
                        :key="'th_role_'+key"
                    >{{role.name}}</th>
                </tr>
                <tr
                    v-for="(user, userKey) in users"
                    :key="'td_user_'+userKey"
                >
                    <td>
                        <div class="users-page__username">
                            <router-link
                                :to="{name: 'user_profile', params: {userId: user.id}}"
                            >{{user.profileName}}</router-link>
                            <div class="users-page__sg-username" title="Username on SteamGifts">{{user.sgProfileName}}</div>
                        </div>
                        <div class="users-page__userid">{{user.steamId}}</div>
                        <div class="users-page__user-links">
                            <a
                                :href="user.profileUrl"
                                target="_blank"
                                class="users-page__user-link"
                            >
                                <i class="icon-fa icon-fa--inline fab fa-steam-symbol"></i>Steam
                            </a>
                            <a
                                :href="'http://steamgifts.com/go/user/'+user.steamId"
                                target="_blank"
                                class="users-page__user-link"
                            >
                                <i class="icon-fa icon-fa--inline far fa-square"></i>Steamgifts
                            </a>
                        </div>
                    </td>
                    <td
                        v-for="(role, key) in roles"
                        :key="'td_user_role_'+userKey+'_'+key"
                    >
                        <!--suppress HtmlFormInputWithoutLabel -->
                        <input
                            type="checkbox"
                            :checked="user.roles.indexOf(role.name) !== -1"
                            :value="role.name"
                            v-model="user.roles"
                            @change="updateRole(user, role.id)"
                            :disabled="isUpdating"
                            :title="role.name"
                        />
                    </td>
                </tr>
            </table>
        </div>

    </div>
</template>

<script>
    import Messagebox from '../components/Messagebox.vue';
    import { mapActions, mapGetters } from 'vuex'

    export default {
        name: "UsersPage",
        components: {
            Messagebox
        },
        data: function () {
            return {
                isLoading: true,
                isUpdating: false,
                errors: []
            };
        },
        computed: {
            ...mapGetters({
                users: 'getManagedUsers'
            }),

            roles() {
                return this.$store.getters.getRoles();
            }
        },
        created () {
            Promise.all([
                this.$store.dispatch('fetchUsers'),
                this.$store.dispatch('fetchRoles')
            ])
                .then(() => {})
                .catch(e => {
                    this.errors = e.response.data.errors ? e.response.data.errors : [{message: e}];
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        methods: {
            ...mapActions(['updateUserRoleState']),

            updateRole(user, roleId) {
                this.isUpdating = true;

                const role = this.$store.getters.getRole(roleId);

                this.updateUserRoleState({
                    user,
                    roleId,
                    active: user.roles.indexOf(role.name) !== -1
                }).then(() => this.isUpdating = false)
            }
        }
    }
</script>

<style lang="less">
    @import "../assets/mixins";

    @import "../assets/blocks/form";

    .users-page {

        &__table-user-col{
            width: 220px;
        }

        &__username{
            font-size: 15px;
            margin-bottom: 4px;
        }
        
        &__sg-username{
            color: @color-blue;
            margin: 4px 0;
        }

        &__userid{
            color: @color-gray;
            margin-bottom: 4px;

            &:before, &:after{
                display: inline;
            }

            &:before{content: '[';}
            &:after{content: ']';}
        }

        &__user-link{
            display: inline-block;

            & + &{
                margin-left: 7px;
            }
        }

    }
</style>