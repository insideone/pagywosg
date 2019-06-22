<template>
    <form
        action=""
        class="utable__row form"
        @submit.prevent="submitNewEntry"
    >
        <div class="utable__row-left">

            <div
                v-if="errors"
                class="utable__line"
            >
                <messagebox
                    v-for="(error, key) in errors"
                    :key="'error_message_'+key"
                    mode="error"
                >{{error.message}}</messagebox>
            </div>

            <div class="utable__line">
                <div class="utable__num utable__col-num">{{eventEntriesCnt+1}}</div>
                <div class="utable__col-select">
                    <div class="form__block form__block--inlined">
                        <span class="form__label">
                        <i class="icon-fa fas fa-user"></i>
                        Player:
                    </span>
                        <a
                            v-if="selectedUser"
                            :href="selectedUser.profileUrl"
                            target="_blank"
                            class="utable__username"
                        >{{selectedUser.profileName}}</a>

                        <div
                            v-if="createHostedEntryAllowed"
                            class="form__block form__block--mb0"
                        >

                            <a
                                v-if="!isUserSearchFormShown && (selectedUser.steamId !== loggedUser.steamId)"
                                @click.prevent="setUserMyself"
                                class="utable__link-control"
                            >
                                <i class="icon-fa icon-fa--inline fas fa-user"></i>Select myself
                            </a>

                            <a
                                v-if="!isUserSearchFormShown"
                                @click.prevent="showUserSearchForm"
                                class="utable__link-control"
                            >
                                <i class="icon-fa icon-fa--inline fas fa-user-secret"></i>Select someone else
                            </a>

                            <template v-else>
                                <div class="form__label form__label--small">
                                    <i class="icon-fa fas fa-edit"></i>
                                    Select another user (by Steam profile link, Steam ID or user name)
                                    &nbsp;
                                    <a
                                        @click.prevent="hideUserSearchForm"
                                        class="utable__link-control"
                                    >
                                        <i class="icon-fa icon-fa--inline fas fa-times"></i>Cancel
                                    </a>
                                </div>

                                <input
                                    v-model="userSearchString"
                                    type="text"
                                    name="user_search"
                                    id="user_search"
                                    class="form__input"
                                    placeholder="Steam profile link, Steam ID or user name..."
                                />
                                <user-variants
                                    ref="searchUser"
                                    :searchString="userSearchString"
                                    @user-selected="selectUser"
                                ></user-variants>
                            </template>

                        </div>
                    </div>
                </div>
                <div class="form__block">
                    <label class="form__label" for="add_game">
                        <i class="icon-fa fab fa-steam-square"></i>
                        Game:
                        <span
                            v-show="isErrorGameNotSelected"
                            class="utable__game-error"
                        >Please select a game</span>
                    </label>
                    <input
                        v-model="gameSearchString"
                        type="text"
                        name="add_game"
                        id="add_game"
                        class="form__input"
                        placeholder="Steam appId or game title..."
                        autocomplete="off"
                        @focus="focusOnGameSearch"
                    />

                    <game-variants
                        ref="searchGame"
                        :searchString="gameSearchString"
                        :isSearching="isGameSearching"
                        @game-selected="selectGame"
                    />

                </div>

            </div>

            <div class="utable__line utable__line--next">
                <div class="utable__category utable__col-select">
                    <div class="form__block form__block--inlined">
                        <label class="form__label" for="add_category">
                            <i class="icon-fa far fa-flag"></i>
                            Subcategory:
                        </label>
                        <select
                            v-model="selectedCategory"
                            required
                            name="add_category" id="add_category" class="form__select"
                            title="The subcategory for the game to qualify for the event"
                        >
                            <option
                                v-for="(category, key) in gameCategories"
                                :key="'add_category_'+key"
                                :value="category.id"
                            >{{category.name}}</option>
                        </select>
                    </div>
                </div>

                <div class="form__block form__block--inlined">
                    <label class="form__label" for="add_notes">
                        <i class="icon-fa far fa-comment"></i>
                        Notes:
                    </label>
                    <textarea
                        v-model="notes"
                        name="add_notes"
                        id="add_notes"
                        class="form__textarea form__textarea--small"
                    ></textarea>
                </div>

                <div class="utable__col-status">
                    <div class="form__block">
                        <label class="form__label" for="add_status">
                            <i class="icon-fa fas fa-clipboard-check"></i>
                            Played Status:
                        </label>
                        <select
                            v-model="selectedPlayStatus"
                            required
                            name="add_status"
                            id="add_status"
                            class="form__select"
                            title="Played status (Unfinished/Beaten/Completed)"
                        >
                            <option
                                v-for="(playStatus, key) in playStatuses"
                                :key="'add_play_status_'+key"
                                :value="playStatus.id"
                            >{{playStatus.name}}</option>
                        </select>
                    </div>
                </div>

            </div>

        </div>
        <div class="utable__row-right">

            <button type="submit" class="button button--margin">
                <i class="icon-fa icon-fa--sized20 fas fa-check"></i>
                Submit
            </button>
            <button
                type="button"
                class="button button--cancel"
                @click="cancelNewRow"
            >
                <i class="icon-fa icon-fa--sized20 fas fa-times"></i>
                Cancel
            </button>

        </div>
    </form>
</template>

<script>
    import GameVariants from './GameVariants.vue';
    import Messagebox from "./Messagebox";

    import {mapActions, mapGetters} from 'vuex';
    import UserVariants from "./UserVariants";


    export default {
        name: "UtableRowAddNew",
        props: {
            eventId: {
                type: Number,
                default: 0
            },
            eventEntriesCnt: {
                type: Number,
                default: 0
            },
            gameCategories: {
                type: Array,
                default: []
            },
            playStatuses: {
                type: Array,
                default: []
            }
        },
        components: {
            UserVariants,
            Messagebox,
            GameVariants
        },
        data: function () {
            return {
                gameSearchString: '',
                isGameSearching: false,
                isErrorGameNotSelected: false,

                errors: [],

                selectedUser: {},
                selectedGame: {},
                selectedCategory: '',
                selectedPlayStatus: '',
                notes: '',

                isUserSearchFormShown: false,
                userSearchString: '',

            };
        },
        computed: {
            ...mapGetters({
                createHostedEntryAllowed: 'isCreatingHostedEntryAllowed'
            }),

            loggedUser: function () {
                return this.$store.getters.getLoggedUser;
            },
        },
        created () {
            this.selectedUser = this.loggedUser;
        },
        methods: {
            ...mapActions(['sendEventEntry']),

            focusOnGameSearch () {
                this.isGameSearching = true;
                this.isErrorGameNotSelected = false;
            },

            cancelNewRow() {
                this.$emit('hide-form');
            },

            selectGame(game) {
                this.errors = [];
                this.selectedGame = game;
                this.isGameSearching = false;
                this.gameSearchString = this.selectedGame.name;
                setTimeout(() => {
                    this.$refs.searchGame.handleSearchStringChange();
                },25);
            },

            submitNewEntry () {
                this.errors = [];

                if (!this.selectedGame.id) {
                    this.isErrorGameNotSelected = true;
                    return;
                }

                let eventEntry = {
                    player: {steamId: this.selectedUser.steamId},
                    eventId: this.eventId,
                    game: {id: this.selectedGame.id},
                    category: {id: this.selectedCategory},
                    playStatus: this.selectedPlayStatus,
                    notes: this.notes,
                };

                this.sendEventEntry(eventEntry).then(({id, errors}) => {
                    if (errors) {
                        this.errors = errors;
                        return;
                    }

                    this.$emit('hide-form');
                })
                    .catch(e => {
                        this.errors = e.response.data.errors ? e.response.data.errors : [{message: e}];
                    });
            },

            setUserMyself() {
                this.selectedUser = this.loggedUser;
            },

            showUserSearchForm() {
                this.isUserSearchFormShown = true;
            },

            hideUserSearchForm(){
                this.isUserSearchFormShown = false;
                this.userSearchString = '';
            },

            selectUser(user) {
                this.errors = [];
                this.selectedUser = user;
                this.hideUserSearchForm();
            }
        }
    }
</script>

<style lang="less">

</style>