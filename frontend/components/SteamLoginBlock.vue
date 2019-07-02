<template>
    <div class="steam-login-block">

        <template v-if="userId">
            <router-link
                :to="{name: 'user_profile', params: {userId: user.id}}"
                class="steam-login-block__user"
            >
                <img
                    :src="user.avatar"
                    :alt="user.profileName"
                    class="steam-login-block__img"
                />
                <span class="steam-login-block__name">{{user.sgProfileName ? user.sgProfileName : user.profileName}}</span>
            </router-link>
            <a :href="'/api/logout'" class="steam-login-block__btn-logout" title="Exit here"><i class="fas fa-sign-out-alt"></i></a>
        </template>

        <form v-else method="post" action="https://steamcommunity.com/openid/login">
            <input type="hidden" name="openid.ns" value="http://specs.openid.net/auth/2.0">
            <input type="hidden" name="openid.mode" value="checkid_setup">
            <input type="hidden" name="openid.identity" value="http://specs.openid.net/auth/2.0/identifier_select">
            <input type="hidden" name="openid.claimed_id" value="http://specs.openid.net/auth/2.0/identifier_select">
            <input type="hidden" name="openid.return_to" :value="returnTo">
            <input type="hidden" name="openid.realm" :value="returnTo">
            <button
                class="steam-login-block__btn-login"
                type="submit"
            ><img src="https://steamcommunity-a.akamaihd.net/public/images/signinthroughsteam/sits_01.png" alt=""/></button>
        </form>

    </div>

</template>

<script>
    export default {
        name: "SteamLoginBlock",
        data: function () {
            return {};
        },
        computed: {
            userId: function () {
              return this.$store.state.loggedUserId;
            },
            user: function(){
                return this.$store.getters.getUser(this.userId);
            },
            returnTo: () => global.location.origin + '/api/login'
        }
    }
</script>

<style lang="less">
    @import "../assets/mixins";

    .steam-login-block{
        padding: 5px 0;
        box-sizing: border-box;
        display: flex;
        align-items: center;

        &__btn-login{
            display: block;
            border: none;
            background: none;
            padding: 0;
            margin: 0;
            cursor: pointer;

            & > img{
                display: block;
            }
        }

        &__user{
            display: flex;
            justify-content: flex-end;
            align-items: center;
            height: 100%;
            text-decoration: none;
            color: @color-blue;
        }

        &__img{
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            margin-right: 10px;
        }

        &__name{
            flex-grow: 1;
        }

        &__btn-logout {
            margin-left: 8px;
        }
    }

</style>
