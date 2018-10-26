<template>
    <div>
        <div class="container" v-if="!articleView">
            <form v-on:submit.prevent="updateArticle">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Title" v-model="article.title">
                </div>
                <div class="form-group">
                    <textarea class="form-control" placeholder="Body" v-model="article.body"></textarea>
                </div>
                <div 
                  class="form-group"
                  v-for="imgUrl in imgUrls"
                  v-bind:key="JSON.stringify(imgUrl)">
                  <img 
                    class="img-resize no-drop"
                    v-bind:src="imgUrl.text" 
                    v-on:click="undoUploadImg(imgUrl)"
                    v-on:error="onImgUploadFail(imgUrl)">
                </div>
                <form v-on:submit.prevent="uploadImg">
                  <div class="form-group">
                      <input type="text" id="img-url" class="form-control" placeholder="Image Url" v-model="imgUrl.text">
                  </div>
                  <button type="submit" class="btn btn-light btn-block">Upload the image</button>
                </form>
                <button type="submit" class="btn btn-light btn-block">Post the article</button>
            </form>

            <hr>

            <nav aria-label="page navigation">
                <ul class="pagination">
                    <li class="page-item"
                        v-bind:class="{'disabled': !pagination.prevPageUrl}">
                        <a class="page-link" 
                            href="#"
                            v-on:click="fetchArticles(pagination.prevPageUrl)">
                            Prev
                        </a>
                    </li>
                    <li class="page-item disabled">
                        <a class="page-link text-dark" href="#">
                            Page {{ pagination.currentPage }} of {{ pagination.lastPage }}
                        </a>
                    </li>
                    <li class="page-item"
                        v-bind:class="{'disabled': !pagination.nextPageUrl}">
                        <a class="page-link" 
                            href="#"
                            v-on:click="fetchArticles(pagination.nextPageUrl)">
                            Next
                        </a>
                    </li>
                </ul>
            </nav>
            <form v-on:submit.prevent="gotoTargetPage">
                <input type="text" id="target-page" placeholder="Page" v-model="targetPage">
                <button type="submit" class="btn btn-light btn-block">Go</button>
            </form>

            <hr>

            <div 
                class="card card-body mb-2"
                v-for="article in articles"
                v-bind:key="article.id">
                <div 
                  class="pointer"
                  v-on:click="openArticle(article)">

                  <h3>{{ article.title }}</h3>
                  <p>{{ article.body.substring(0, 10) }}{{ (article.body.length > 10) ? "..." : "" }}</p>
                  <p>Authored by <b>{{ article.author }}</b> at {{ article.created_at }}</p>
                  <p>Edited by <b>{{ article.editor || article.author }}</b></p>
                  <p>Last viewed at {{ article.updated_at }}</p>
                  <p>{{ `${article.view_count} ${(article.view_count > 1) ?  'views' : 'view'}` }}</p>
                  <div
                    v-for="imgUrl in JSON.parse(article.img_urls)"
                    v-bind:key="imgUrl.id"
                    v-if="imgUrl.id === 0">
                    <img class="img-resize" v-bind:src="imgUrl.text">
                  </div>
                  
                </div>
                <hr>
                <template v-if="strcmp(user, article.author) === 0 || isAdmin">
                    <button 
                        class="btn btn-warning mb-2"
                        v-on:click="editArticle(article)">
                        Edit
                    </button>
                    <button 
                        class="btn btn-danger"
                        v-on:click="deleteArticle(article)">
                        Delete
                    </button>
                </template>
            </div>
        </div>

        <div class="container" v-if="articleView">
            <h3>{{ article.title }}</h3>
            <p>{{ article.body }}</p>
            <p>Authored by <b>{{ article.author }}</b></p>
            <p>Edited by <b>{{ article.editor || article.author }}</b></p>
            <div
              v-for="imgUrl in JSON.parse(article.imgUrls)"
              v-bind:key="JSON.stringify(imgUrl)">
              <a
                target="_blank"
                v-bind:href="imgUrl.text">
                <img class="img-resize mb-2" v-bind:src="imgUrl.text">
              </a>
            </div>
            <!-- IMAGE EXAMPLE
            <a 
              target="_blank"
              v-bind:href="'https://www.thefamouspeople.com/profiles/images/martyn-ford-3.jpg'">
              <img class="img-resize" v-bind:src="'https://www.thefamouspeople.com/profiles/images/martyn-ford-3.jpg'">
            </a>
            <img class="img-resize" v-bind:src="'http://neverfearfailure.com/wp-content/uploads/2017/05/Martyn-Ford.jpg'">
            -->
            <button 
                class="btn btn-warning mb-2"
                v-on:click="closeArticle()">Back</button>
            
            <hr>

            <h2>Comments</h2>
            <div 
                class="card card-body mb-2"
                v-for="comment in comments"
                v-bind:key="comment.id">
                <p>{{ comment.body }}</p>
                <p>By <b>{{ comment.author }}</b>  at {{ comment.updated_at }}</p>
                <hr>
            </div>
            <form v-on:submit.prevent="addComment">
                <div class="form-group">
                    <textarea class="form-control" placeholder="Body" v-model="comment.body"></textarea>
                </div>
                <button type="submit" class="btn btn-light btn-block">Submit</button>
            </form>
        </div>
    </div>
</template>

<script>
import * as string from './mixins/string.js';

export default {
  props: {
    user: {
      type: String
    },
    fetchAll: {
      type: Boolean
    }
  },
  data() {
    return {
      articles: [],
      article: {
        id: "",
        title: "",
        body: "",
        author: "",
        editor: undefined,
        imgUrls: ""
      },
      comments: [],
      comment: {
        body: "",
        author: "",
        articleId: ""
      },
      pagination: "",
      editMode: false,
      targetPage: "",
      articleView: false,
      imgUrls: [],
      imgUrl: {
        id: 0,
        text: ""
      }
    };
  },
  created() {
    this.fetchArticles(
      `/index.php/api/articles${this.fetchAll ? "" : `/${this.user}`}`
    );
  },
  computed: {
    isAdmin() {
      return this.strcmp(this.user, "admin") === 0;
    }
  },
  methods: {
    strcmp: string.strcmp,

    fetchArticles(pageUrl, callback) {
      fetch(pageUrl)
        .then(res => res.json())
        .then(res => {
          //console.log(res.data);
          this.articles = res.data;
          this.makePagination(res.meta, res.links);
          this.imgUrls = [];
          if (callback) callback();
        })
        .catch(err => console.log(err));
    },
    makePagination(meta, links) {
      let pagination = {
        currentPage: meta.current_page,
        lastPage: meta.last_page,
        prevPageUrl: links.prev,
        nextPageUrl: links.next
      };
      this.pagination = pagination;
    },
    deleteArticle(article) {
      if (confirm("Please confirm the deletion")) {
        fetch(`/index.php/api/article/${article.id}`, {
          method: "delete"
        })
          .then(res => res.json)
          .then(res => {
            this.fetchArticles(
              `/index.php/api/articles${this.fetchAll ? "" : `/${this.user}`}`,
              function() {
                alert("the article has been removed");
              }
            );
          })
          .catch(err => console.log(err));
      }
    },
    updateArticle() {
      const instance = this;
      const func = function(method, msg) {
        if (!instance.article.title || !instance.article.body) {
          alert('The title and body are required');
          return;
        }

        if (instance.editMode)
          instance.article.editor = instance.user;
        else
          instance.article.author = instance.article.editor = instance.user;
        for (var i = 0; i < instance.imgUrls.length; ++i)
          instance.imgUrls[i].id = i;
        instance.article.imgUrls = JSON.stringify(instance.imgUrls);
        instance.imgUrls = [];

        fetch("/index.php/api/article", {
          method: method,
          body: JSON.stringify(instance.article),
          headers: {
            "content-type": "application/json"
          }
        })
          .then(res => res.json())
          .then(res => {
            instance.article.title = "";
            instance.article.body = "";
            instance.fetchArticles(
              `/index.php/api/articles${
                instance.fetchAll ? "" : `/${instance.user}`
              }`,
              function() {
                alert(msg);
              }
            );
          })
          .catch(err => console.log(err));
      };
      if (this.editMode) func("put", "The article has been edited");
      else func("post", "An article has been created");
    },
    editArticle(article) {
      this.editMode = true;
      this.article.id = article.id;
      this.article.title = article.title;
      this.article.body = article.body;
      this.article.author = article.author;
      this.imgUrls = JSON.parse(article.img_urls);

      var href = window.location.href;
      if (href[href.length - 1] === '#')
        href = href.substring(0, href.length - 1);
      window.location.href = `${href}#`;
    },
    gotoTargetPage() {
      const targetPage = parseInt(this.targetPage);
      if (isNaN(targetPage)) 
        alert("The page needs to be an integer");
      else if (Math.min(Math.max(1, targetPage), this.pagination.lastPage) !== targetPage)
        alert("The page is out of range");
      else
        this.fetchArticles(
          `/index.php/api/articles${
            this.fetchAll ? "" : `/${this.user}`
          }?page=${this.targetPage}`
        );

      document.getElementById("target-page").value = this.targetPage = "";
    },
    openArticle(article) {
      this.articleView = true;

      this.article.id = article.id;
      this.article.title = article.title;
      this.article.body = article.body;
      this.article.author = article.author;
      this.article.editor = article.editor;
      this.article.imgUrls = article.img_urls;

      this.fetchComments(`/index.php/api/comments/${article.id}`);
      fetch(`/index.php/api/article/${article.id}`, {
        method: 'post'
      });
    },
    closeArticle() {
      this.articleView = false;

      this.article.id = "";
      this.article.title = "";
      this.article.body = "";

      this.fetchArticles(`/index.php/api/articles${this.fetchAll ? "" : `/${this.user}`}`);
    },
    fetchComments(pageUrl, callback) {
      fetch(pageUrl)
        .then(res => res.json())
        .then(res => {
          this.comments = res.data;
          if (callback) callback();
        })
        .catch(err => console.log(err));
    },
    addComment() {
      const instance = this;
      const func = function(method, msg) {
        instance.comment.author = instance.user;
        instance.comment.articleId = instance.article.id;
        fetch("/index.php/api/comment", {
          method: method,
          body: JSON.stringify(instance.comment),
          headers: {
            "content-type": "application/json"
          }
        })
          .then(res => res.json())
          .then(res => {
            instance.comment.body = "";
            instance.fetchComments(
              `/index.php/api/comments/${instance.article.id}`,
              function() {
                alert(msg);
              }
            );
          })
          .catch(err => console.log(err));
      };
      func("post", "A comment has been created");
    },
    uploadImg() {
      this.imgUrls.push({
        id: this.imgUrl.id,
        text: this.imgUrl.text
      });
      ++this.imgUrl.id;
      document.getElementById("img-url").value = this.imgUrl.text = "";
    },
    undoUploadImg(imgUrl) {
      for (var i = this.imgUrls.length - 1; i >= 0; --i) {
        if (this.imgUrls[i].id === imgUrl.id) {
          this.imgUrls.splice(i, 1);
          break;
        }
      }
    },
    onImgUploadFail(imgUrl) {
      this.undoUploadImg(imgUrl);
      alert('the image url is invalid');
    }
  }
};
</script>