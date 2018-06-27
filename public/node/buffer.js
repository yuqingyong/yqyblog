// const buf = Buffer.from('runoob','ascii');

// //输出 72756e6f6f62
// console.log(buf.toString('hex'));

// //输出 cnVub29i
// console.log(buf.toString('base64'));

//创建一个长度为256的buffer实例，并且使用0填充
// buf = Buffer.alloc(256);

//写入数据
// len = buf.write("www.yuqingyong.cn");

// console.log("写入字节数："+len);

//读取数据
// buf = Buffer.alloc(26);
// for(var i = 0;i<26;i++){
// 	buf[i] = i+97;
// }

// console.log( buf.toString('ascii'));       // 输出: abcdefghijklmnopqrstuvwxyz
// console.log( buf.toString('ascii',0,5));   // 输出: abcde
// console.log( buf.toString('utf8',0,5));    // 输出: abcde
// console.log( buf.toString(undefined,0,5)); // 使用 'utf8' 编码, 并输出: abcde

// const buf = Buffer.from([0x1,0x2,0x3,0x4,0x5]);
// const json = JSON.stringify(buf);

// // //输出: {"type":"Buffer","data":[1,2,3,4,5]}
// // console.log(json);

// const copy = JSON.parse(json,(key,value)=>{
// 	return value && value.type === 'Buffer' ? Buffer.from(value.data) : value;
// });

// console.log(copy);

// var buffer1 = Buffer.from(('星辰博客'));
// var buffer2 = Buffer.from(('www.yuqingyong.cn'));

// var buffer3 = Buffer.concat([buffer1,buffer2]);

// console.log(buffer3.toString());

// var buffer1 = Buffer.from('ABC');
// var buffer2 = Buffer.from('ABCD');

// var result = buffer1.compare(buffer2);

// //  <0 在之前   0 相同   1在之后
// console.log(result);

// //拷贝缓冲区
// var buf1 = Buffer.from('abcdefgasdasdadsa');
// var buf2 = Buffer.from('RUNOOB');

// //将buf2插入到buf1指定位置上
// buf2.copy(buf1,2);
// console.log(buf1.toString());

// var buffer1 = Buffer.from('runoob');
// //剪切缓冲区
// var buffer2 = buffer1.slice(0,2);
// console.log(buffer2.length);