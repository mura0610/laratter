<?php
use App\Models\Tweet;
use App\Models\User;

// tests/Feature/TweetTest.php

// 作成処理のテスト
it('allows authenticated users to create a tweet', function () {
  // ユーザを作成
  $user = User::factory()->create();

  // ユーザを認証
  $this->actingAs($user);

  // Tweetを作成
  $tweetData = ['tweet' => 'This is a test tweet.'];

  // POSTリクエスト
  $response = $this->post('/tweets', $tweetData);

  // データベースに保存されたことを確認
  $this->assertDatabaseHas('tweets', $tweetData);

  // レスポンスの確認
  $response->assertStatus(302);
  $response->assertRedirect('/tweets');
});


  it('can search tweets by content keyword', function () {
  $user = User::factory()->create();
  $this->actingAs($user);

  // キーワードを含むツイートを作成
  Tweet::factory()->create([
    'tweet' => 'This is a test tweet',
    'user_id' => $user->id,
  ]);

  // キーワードを含まないツイートを作成
  Tweet::factory()->create([
    'tweet' => 'This is another tweet',
    'user_id' => $user->id,
  ]);

  // キーワード "test" で検索
  $response = $this->get(route('tweets.search', ['keyword' => 'test']));

  $response->assertStatus(200);
  $response->assertSee('This is a test tweet');
  $response->assertDontSee('This is another tweet');
});

it('shows no tweets if no match found', function () {
  $user = User::factory()->create();
  $this->actingAs($user);

  Tweet::factory()->create([
    'tweet' => 'This is a tweet',
    'user_id' => $user->id,
  ]);

  // 存在しないキーワードで検索
  $response = $this->get(route('tweets.search', ['keyword' => 'nonexistent']));

  $response->assertStatus(200);
  $response->assertDontSee('This is a tweet');
  $response->assertSee('No tweets found.');
});

